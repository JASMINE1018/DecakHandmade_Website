<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;

class StockAdjustmentController extends Controller
{
    public function store(StockAdjustmentRequest $request, Product $product, InventoryService $inventory): RedirectResponse
    {
        $data = $request->validated();

        $inventory->adjust(
            $product,
            $data['mode'],
            (int) $data['quantity'],
            $data['reason'],
            $data['note'],
            $request->user('admin'),
        );

        return back()->with('success', 'Stok diperbarui.');
    }
}
