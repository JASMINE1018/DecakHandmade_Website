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
        return Auth::guard('customer')->user() ?? Auth::user();
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
            return redirect()->route('user.cart')
                ->with('error', 'Keranjang belanja Anda masih kosong.');
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
            'orders_url' => route('user.orders'),
        ], 201);
    }
}
