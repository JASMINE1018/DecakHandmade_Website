<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CheckoutRequest;
use App\Models\Customer;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
    ) {}

    protected function getCustomer(): Customer
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();

        return $customer;
    }

    public function cart(): View
    {
        $cart = $this->cart->summary();

        return view('dashboard-user.cart', compact('cart'));
    }

    public function index(): View|RedirectResponse
    {
        $cart = $this->cart->summary();

        if ($cart['items'] === []) {
            return redirect()->route('catalog.index')
                ->with('error', 'Your shopping cart is empty.');
        }

        $customer = $this->getCustomer();
        $addresses = $customer->addresses()->orderByDesc('is_primary')->latest()->get();
        $primaryAddress = $addresses->first();

        return view('dashboard-user.checkout', compact('customer', 'addresses', 'primaryAddress', 'cart'));
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        $customer = $this->getCustomer();
        $order = $this->checkout->placeOrder($customer, $request->validated());

        return response()->json([
            'order_number' => $order->order_number,
            'grand_total' => $order->grand_total,
            'status' => $order->status,
            'payment_method' => $order->payment_method ?? 'bank',
            'payment_status' => $order->payment_status ?? 'pending',
            'payment_bank' => $order->payment_bank ?? config('midtrans.manual_bank.bank_name', 'BCA'),
            'payment_va_number' => $order->payment_va_number ?? config('midtrans.manual_bank.account_number', '1234567890'),
            'payment_holder' => config('midtrans.manual_bank.account_holder', 'Decak Handmade'),
            'payment_qris_url' => $order->payment_qris_url,
            'orders_url' => route('user.orders'),
        ], 201);
    }
}
