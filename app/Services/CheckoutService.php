<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PricingService $pricing,
        private readonly InventoryService $inventory,
    ) {}

    /**
     * Proses checkout pelanggan menjadi pesanan nyata di database.
     *
     * @param  array<string, mixed>  $data
     */
    public function placeOrder(Customer $customer, array $data): Order
    {
        $rawItems = $this->cart->rawItems();

        if ($rawItems === []) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang belanja Anda masih kosong.',
            ]);
        }

        $quote = $this->pricing->quote($rawItems);

        if ($quote['items'] === []) {
            throw ValidationException::withMessages([
                'cart' => 'Tidak ada produk valid di keranjang belanja.',
            ]);
        }

        return DB::transaction(function () use ($customer, $data, $quote) {
            $subtotal = $quote['subtotal'];
            $shippingCost = 0;

            $paymentLabel = match ($data['payment_method'] ?? 'bank') {
                'ewallet' => 'Dompet Digital',
                default => 'Transfer Bank',
            };

            $addressLine = trim(implode(', ', array_filter([
                $data['full_address'] ?? null,
                $data['city'] ?? null,
                $data['province'] ?? null,
                $data['postal_code'] ?? null,
            ])));

            $noteParts = array_filter([
                "Penerima: {$data['recipient_name']} ({$data['phone']})",
                $addressLine !== '' ? "Alamat: {$addressLine}" : null,
                "Pembayaran: {$paymentLabel}",
                ! empty($data['customer_note']) ? 'Catatan: '.trim((string) $data['customer_note']) : null,
            ]);

            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => Order::STATUS_PROCESSING,
                'source' => 'storefront',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'grand_total' => $subtotal + $shippingCost,
                'customer_note' => mb_substr(implode(' | ', $noteParts), 0, 500),
                'created_by' => null,
            ]);

            $prefix = strtoupper((string) Setting::get('order_prefix', 'ORD')) ?: 'ORD';

            $order->forceFill([
                'order_number' => $prefix.'-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
            ])->save();

            foreach ($quote['items'] as $line) {
                $item = $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'sku' => $line['product']->sku,
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                    'note' => $line['note'],
                ]);

                if ($line['variant']) {
                    $item->variants()->create([
                        'variant_id' => $line['variant']->id,
                        'variant_name' => $line['variant']->name,
                        'price_adjustment' => $line['variant']->price_adjustment,
                    ]);
                }

                foreach ($line['customizations'] as $customization) {
                    $item->customizations()->create([
                        'option_id' => $customization['option']->id,
                        'option_name' => $customization['option_name'],
                        'input_type' => $customization['input_type'],
                        'value' => $customization['value'],
                        'price' => $customization['price'],
                    ]);
                }
            }

            $this->inventory->deductForOrder($order, null);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => Order::STATUS_PROCESSING,
                'note' => "Pesanan dibuat melalui checkout web ({$paymentLabel})",
                'changed_by' => null,
                'created_at' => now(),
            ]);

            if (! empty($data['save_as_primary'])) {
                $customer->addresses()->update(['is_primary' => false]);

                $customer->addresses()->create([
                    'label' => 'Utama',
                    'recipient_name' => $data['recipient_name'],
                    'phone' => $data['phone'],
                    'full_address' => $data['full_address'],
                    'province' => $data['province'],
                    'city' => $data['city'],
                    'subdistrict' => $data['subdistrict'] ?? '',
                    'postal_code' => $data['postal_code'],
                    'is_primary' => true,
                ]);
            }

            $this->cart->clear();

            return $order->fresh(['items.variants', 'items.customizations', 'customer']);
        });
    }
}
