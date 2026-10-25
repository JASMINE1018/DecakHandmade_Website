<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly PricingService $pricing,
        private readonly InventoryService $inventory,
        private readonly AuditLogService $audit,
    ) {}

    public function create(array $data, Admin $admin): Order
    {
        $quote = $this->pricing->quote($data['items'] ?? []);

        if ($quote['items'] === []) {
            throw ValidationException::withMessages(['items' => 'Pesanan harus memiliki minimal satu item.']);
        }

        return DB::transaction(function () use ($data, $admin, $quote) {
            $customer = $this->resolveCustomer($data);

            $subtotal = $quote['subtotal'];
            $shipping = max(0, (int) ($data['shipping_cost'] ?? 0));

            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => Order::STATUS_PROCESSING,
                'source' => 'manual',
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'grand_total' => $subtotal + $shipping,
                'customer_note' => $data['customer_note'] ?? null,
                'admin_note' => $data['admin_note'] ?? null,
                'created_by' => $admin->id,
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

            $this->inventory->deductForOrder($order, $admin);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => Order::STATUS_PROCESSING,
                'note' => 'Pesanan dibuat',
                'changed_by' => $admin->id,
                'created_at' => now(),
            ]);

            $this->audit->record('order.created', $order, null, [
                'order_number' => $order->order_number,
                'subtotal' => $order->subtotal,
                'grand_total' => $order->grand_total,
            ], $admin);

            return $order->fresh(['items.variants', 'items.customizations', 'customer']);
        });
    }

    public function updateNotes(Order $order, array $data, Admin $admin): Order
    {
        return DB::transaction(function () use ($order, $data, $admin) {
            /** @var Order $locked */
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($locked->isFinal()) {
                throw ValidationException::withMessages([
                    'order' => 'Pesanan Selesai atau Dibatalkan tidak dapat diubah.',
                ]);
            }

            $before = $locked->only(['customer_note', 'admin_note', 'shipping_cost', 'grand_total']);

            $locked->fill([
                'customer_note' => $data['customer_note'] ?? null,
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            if (isset($data['shipping_cost'])) {
                $shipping = max(0, (int) $data['shipping_cost']);
                $locked->shipping_cost = $shipping;
                $locked->grand_total = $locked->subtotal + $shipping;
            }

            $locked->save();

            $this->audit->record('order.updated', $locked, $before, $locked->only(array_keys($before)), $admin);

            return $locked;
        });
    }

    public function transition(Order $order, string $to, ?string $note, Admin $admin, bool $returned = false): Order
    {
        return DB::transaction(function () use ($order, $to, $note, $admin, $returned) {
            /** @var Order $locked */
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);
            $from = $locked->status;

            $allowed = config("order_status.transitions.{$from}", []);

            if (! in_array($to, $allowed, true)) {
                throw ValidationException::withMessages([
                    'status' => 'Transisi status tidak diizinkan.',
                ]);
            }

            if ($to === Order::STATUS_CANCELLED && $from === Order::STATUS_SHIPPED && ! $returned) {
                throw ValidationException::withMessages([
                    'returned' => 'Pembatalan dari status Dikirim memerlukan konfirmasi barang kembali.',
                ]);
            }

            $locked->status = $to;

            if ($to === Order::STATUS_COMPLETED) {
                $locked->completed_at = $locked->completed_at ?? now();
            }

            $locked->save();

            if ($to === Order::STATUS_CANCELLED) {
                $this->inventory->restoreForOrder($locked, $admin, 'cancel');
            }

            OrderStatusHistory::create([
                'order_id' => $locked->id,
                'from_status' => $from,
                'to_status' => $to,
                'note' => $note,
                'changed_by' => $admin->id,
                'created_at' => now(),
            ]);

            $this->audit->record('order.status_changed', $locked, ['status' => $from], ['status' => $to, 'note' => $note], $admin);

            return $locked->fresh();
        });
    }

    public function cancel(Order $order, string $reason, string $note, Admin $admin, bool $returned = false): Order
    {
        $label = config("order_status.cancel_reasons.{$reason}", $reason);
        $fullNote = trim($label.'. '.$note);

        return $this->transition($order, Order::STATUS_CANCELLED, $fullNote, $admin, $returned);
    }

    private function resolveCustomer(array $data): Customer
    {
        if (($data['customer_mode'] ?? 'existing') === 'existing') {
            $customer = Customer::find($data['customer_id'] ?? null);

            if (! $customer) {
                throw ValidationException::withMessages(['customer_id' => 'Pelanggan tidak ditemukan.']);
            }

            return $customer;
        }

        $phone = Customer::normalizePhone($data['customer_phone'] ?? '');

        if ($phone === '') {
            throw ValidationException::withMessages(['customer_phone' => 'Nomor telepon wajib diisi.']);
        }

        if (Customer::where('phone', $phone)->exists()) {
            throw ValidationException::withMessages([
                'customer_phone' => 'Nomor telepon sudah terdaftar. Pilih pelanggan yang ada.',
            ]);
        }

        return Customer::create([
            'name' => $data['customer_name'],
            'phone' => $phone,
            'email' => $data['customer_email'] ?? null,
            'address' => $data['customer_address'] ?? null,
        ]);
    }
}
