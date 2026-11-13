<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
    ) {}

    public function summary(): JsonResponse
    {
        return response()->json($this->cart->summary());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'customizations' => ['nullable', 'array'],
            'customizations.*.option_id' => ['required_with:customizations', 'integer'],
            'customizations.*.value' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        return response()->json($this->cart->addItem($validated), 201);
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        return response()->json($this->cart->updateQuantity($key, (int) $validated['quantity']));
    }

    public function destroy(string $key): JsonResponse
    {
        return response()->json($this->cart->removeItem($key));
    }
}
