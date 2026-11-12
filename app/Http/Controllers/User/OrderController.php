<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected function getCustomer(): Customer
    {
        return Auth::guard('customer')->user() ?? Auth::user();
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
                'items' => $order->items->map(function ($item) {
                    $firstImg = null;
                    if ($item->product && is_array($item->product->gallery) && count($item->product->gallery) > 0) {
                        $firstImg = '/storage/' . $item->product->gallery[0];
                    }
                    return [
                        'title' => $item->product_name,
                        'variant' => 'SKU: ' . ($item->sku ?? '-'),
                        'qty' => $item->quantity,
                        'price' => $item->unit_price,
                        'image' => $firstImg ?? '/images/products/tote-senja.png',
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
