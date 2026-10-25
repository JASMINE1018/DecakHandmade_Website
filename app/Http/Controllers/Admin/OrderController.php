<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('customer')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(fn ($q) => $q
                    ->where('order_number', 'like', $term)
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $term)));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('admin.orders.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::query()
                ->active()
                ->with(['variants' => fn ($q) => $q->where('is_active', true), 'customizationOptions' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreOrderRequest $request, OrderService $service): RedirectResponse
    {
        $order = $service->create($request->validated(), $request->user('admin'));

        return redirect()->route('admin.orders.show', $order)->with('success', 'Pesanan '.$order->order_number.' dibuat.');
    }

    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'creator',
            'items.variants',
            'items.customizations',
            'statusHistories.actor',
        ]);

        return view('admin.orders.show', [
            'order' => $order,
            'transitions' => config("order_status.transitions.{$order->status}", []),
            'cancelReasons' => config('order_status.cancel_reasons'),
        ]);
    }

    public function edit(Order $order): View|RedirectResponse
    {
        if ($order->isFinal()) {
            return redirect()
                ->route('admin.orders.show', $order)
                ->withErrors(['order' => 'Pesanan Selesai atau Dibatalkan tidak dapat diubah.']);
        }

        return view('admin.orders.edit', ['order' => $order]);
    }

    public function update(UpdateOrderRequest $request, Order $order, OrderService $service): RedirectResponse
    {
        $service->updateNotes($order, $request->validated(), $request->user('admin'));

        return redirect()->route('admin.orders.show', $order)->with('success', 'Pesanan diperbarui.');
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderService $service): RedirectResponse
    {
        $data = $request->validated();

        $service->transition(
            $order,
            $data['status'],
            $data['note'] ?? null,
            $request->user('admin'),
            $request->boolean('returned'),
        );

        return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan diperbarui.');
    }

    public function cancel(CancelOrderRequest $request, Order $order, OrderService $service): RedirectResponse
    {
        $data = $request->validated();

        $service->cancel(
            $order,
            $data['reason'],
            $data['note'],
            $request->user('admin'),
            $request->boolean('returned'),
        );

        return redirect()->route('admin.orders.show', $order)->with('success', 'Pesanan dibatalkan.');
    }
}
