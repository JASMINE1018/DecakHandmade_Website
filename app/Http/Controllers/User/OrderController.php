<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService
    ) {}

    protected function getCustomer(): Customer
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return $customer;
    }

    public function index(Request $request)
    {
        $customer = $this->getCustomer();
        $status = $request->query('status', 'all');

        $query = $customer->orders()->with(['items.product', 'statusHistories'])->latest();

        if ($status === 'processing') {
            $query->where('status', Order::STATUS_PROCESSING);
        } elseif ($status === 'shipping' || $status === 'shipped') {
            $query->where('status', Order::STATUS_SHIPPED);
        } elseif ($status === 'completed') {
            $query->where('status', Order::STATUS_COMPLETED);
        } elseif ($status === 'cancelled') {
            $query->where('status', Order::STATUS_CANCELLED);
        }

        $orders = $query->get()->map(function ($order) {
            $statusLabel = match ($order->status) {
                'processing' => 'Processing',
                'shipped' => 'In Delivery',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                default => ucfirst($order->status),
            };

            $statusClass = match ($order->status) {
                'processing' => 'bg-[#faf0eb] text-[#c06547]',
                'shipped' => 'bg-[#ede6dc] text-[#5e5449]',
                'completed' => 'bg-[#eaf0e7] text-[#5a7355]',
                'cancelled' => 'bg-[#fdf3ee] text-[#b93826]',
                default => 'bg-[#ede6dc] text-[#5e5449]',
            };

            $paymentMethod = $order->payment_method ?? 'bank';
            $paymentBank = $order->payment_bank ?? config('midtrans.manual_bank.bank_name', 'BCA');
            $paymentVaNumber = $order->payment_va_number ?? config('midtrans.manual_bank.account_number', '1234567890');
            $paymentHolder = config('midtrans.manual_bank.account_holder', 'Decak Handmade');
            $paymentQrisUrl = $order->payment_qris_url;

            // Generate QRIS live jika metode ewallet tapi QRIS URL belum tersedia
            if ($paymentMethod === 'ewallet' && empty($paymentQrisUrl) && in_array($order->status, [Order::STATUS_PROCESSING, 'pending'], true)) {
                if ($this->midtransService->isConfigured()) {
                    $paymentRes = $this->midtransService->chargeQris($order);
                    if (! empty($paymentRes['qris_url'])) {
                        $paymentQrisUrl = $paymentRes['qris_url'];
                        $order->update([
                            'payment_qris_url' => $paymentQrisUrl,
                            'payment_status' => $paymentRes['status'] ?? $order->payment_status,
                            'payment_raw_response' => $paymentRes['raw'] ?? null,
                        ]);
                    }
                }
            }

            return [
                'id' => $order->order_number,
                'date' => $order->created_at->translatedFormat('d F Y'),
                'status' => $order->status,
                'statusLabel' => $statusLabel,
                'statusClass' => $statusClass,
                'total' => $order->grand_total,
                'courier' => $order->courier ?? 'Kurir Decak',
                'trackingNumber' => $order->tracking_number ?? '-',
                'estimate' => $order->completed_at ? 'Tiba ' . $order->completed_at->translatedFormat('d F Y') : 'Estimasi 2–4 hari kerja',
                'paymentMethod' => $paymentMethod,
                'paymentStatus' => $order->payment_status ?? 'pending',
                'paymentBank' => $paymentBank,
                'paymentVaNumber' => $paymentVaNumber,
                'paymentHolder' => $paymentHolder,
                'paymentQrisUrl' => $paymentQrisUrl,
                'items' => $order->items->map(function ($item) {
                    return [
                        'title' => $item->product_name,
                        'variant' => 'SKU: ' . ($item->sku ?? '-'),
                        'qty' => $item->quantity,
                        'price' => $item->unit_price,
                        'image' => $item->product ? $item->product->getImageUrl('thumb') : '/images/products/tote-senja.png',
                    ];
                }),
                'history' => $order->statusHistories->map(function ($h) {
                    return [
                        'time' => $h->created_at->translatedFormat('d M Y H:i'),
                        'desc' => $h->note ?? ('Status diubah ke ' . $h->to_status),
                    ];
                }),
            ];
        });

        return view('dashboard-user.orders', compact('customer', 'orders', 'status'));
    }
}
