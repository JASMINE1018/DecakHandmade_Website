<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    public function bounds(string $start, string $end): array
    {
        return [
            Carbon::parse($start, 'Asia/Jakarta')->startOfDay()->utc(),
            Carbon::parse($end, 'Asia/Jakarta')->endOfDay()->utc(),
        ];
    }

    public function summary(string $start, string $end): array
    {
        [$from, $to] = $this->bounds($start, $end);

        $orders = Order::query()->completed()->whereBetween('completed_at', [$from, $to]);

        $revenue = (int) (clone $orders)->sum('subtotal');
        $count = (int) (clone $orders)->count();

        $units = (int) OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->whereBetween('orders.completed_at', [$from, $to])
            ->sum('order_items.quantity');

        $firstOrders = DB::table('orders')
            ->selectRaw('customer_id, MIN(created_at) as first_at')
            ->where('status', '<>', Order::STATUS_CANCELLED)
            ->groupBy('customer_id');

        $newCustomers = (int) DB::query()
            ->fromSub($firstOrders, 't')
            ->whereBetween('first_at', [$from, $to])
            ->count();

        return [
            'revenue' => $revenue,
            'orders' => $count,
            'units' => $units,
            'average' => $count > 0 ? (int) round($revenue / $count) : 0,
            'new_customers' => $newCustomers,
            'start' => $start,
            'end' => $end,
        ];
    }

    public function daily(string $start, string $end): Collection
    {
        [$from, $to] = $this->bounds($start, $end);

        $rows = Order::query()
            ->completed()
            ->whereBetween('completed_at', [$from, $to])
            ->selectRaw("DATE(CONVERT_TZ(completed_at, '+00:00', '+07:00')) as day, SUM(subtotal) as revenue, COUNT(*) as orders")
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $units = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->whereBetween('orders.completed_at', [$from, $to])
            ->selectRaw("DATE(CONVERT_TZ(orders.completed_at, '+00:00', '+07:00')) as day, SUM(order_items.quantity) as units")
            ->groupBy('day')
            ->pluck('units', 'day');

        $result = collect();
        $cursor = Carbon::parse($start, 'Asia/Jakarta')->startOfDay();
        $last = Carbon::parse($end, 'Asia/Jakarta')->startOfDay();

        while ($cursor->lte($last)) {
            $key = $cursor->toDateString();
            $row = $rows->get($key);

            $result->push([
                'date' => $key,
                'revenue' => (int) ($row->revenue ?? 0),
                'orders' => (int) ($row->orders ?? 0),
                'units' => (int) ($units->get($key) ?? 0),
            ]);

            $cursor->addDay();
        }

        return $result;
    }

    public function topProducts(string $start, string $end, int $limit = 5): Collection
    {
        [$from, $to] = $this->bounds($start, $end);

        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::STATUS_COMPLETED)
            ->whereBetween('orders.completed_at', [$from, $to])
            ->selectRaw('order_items.product_id, MAX(order_items.product_name) as product_name, SUM(order_items.quantity) as units, SUM(order_items.line_total) as revenue, COUNT(DISTINCT order_items.order_id) as orders')
            ->groupBy('order_items.product_id')
            ->orderByDesc('units')
            ->orderByDesc('revenue')
            ->orderBy('product_name')
            ->limit($limit)
            ->get();
    }

    public function dashboard(): array
    {
        $today = now('Asia/Jakarta')->toDateString();
        $summary = $this->summary($today, $today);
        $weekStart = now('Asia/Jakarta')->subDays(6)->toDateString();
        [$from, $to] = $this->bounds($today, $today);

        $newOrders = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '<>', Order::STATUS_CANCELLED)
            ->count();

        return [
            'today' => $summary,
            'new_orders' => $newOrders,
            'processing' => Order::query()->where('status', Order::STATUS_PROCESSING)->count(),
            'low_stock' => Product::query()->active()->whereColumn('stock', '<=', 'min_stock')->count(),
            'daily' => $this->daily($weekStart, $today),
            'latest_orders' => Order::query()->with('customer')->latest('id')->limit(5)->get(),
            'week_start' => $weekStart,
            'today_date' => $today,
        ];
    }
}
