<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVariantRequest;
use App\Http\Requests\UpdateVariantRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\AuditLogService;
use App\Services\ImageStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VariantController extends Controller
{
    public function store(StoreVariantRequest $request, Product $product, ImageStorageService $images): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image']);

        $variant = $product->variants()->create($data + ['sort_order' => 0]);

        if ($request->hasFile('image')) {
            $variant->update(['image_path' => $images->storeVariantImage($request->file('image'))]);
        }

        app(AuditLogService::class)->record('variant.created', $variant, null, $variant->only(['name', 'type', 'price_adjustment']), $request->user('admin'));

        return back()->with('success', 'Variasi ditambahkan.');
    }

    public function update(UpdateVariantRequest $request, Product $product, ProductVariant $variant, ImageStorageService $images): RedirectResponse
    {
        $data = $request->validated();
        unset($data['image']);

        $type = $data['type'] ?? $variant->type;

        if ($type === 'motif' && ! $request->hasFile('image') && ! $variant->image_path) {
            throw ValidationException::withMessages([
                'image' => 'Variasi motif wajib memiliki gambar.',
            ]);
        }

        $oldImage = $variant->image_path;
        $variant->fill($data);

        if ($request->hasFile('image')) {
            $variant->image_path = $images->storeVariantImage($request->file('image'));
        }

        $variant->save();

        if ($request->hasFile('image') && $oldImage) {
            $images->delete($oldImage);
        }

        app(AuditLogService::class)->record('variant.updated', $variant, null, $variant->only(['name', 'type', 'price_adjustment']), $request->user('admin'));

        return back()->with('success', 'Variasi diperbarui.');
    }

    public function destroy(Request $request, Product $product, ProductVariant $variant, ImageStorageService $images): RedirectResponse
    {
        $used = DB::table('order_item_variants')
            ->join('order_items', 'order_items.id', '=', 'order_item_variants.order_item_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('order_item_variants.variant_id', $variant->id)
            ->whereIn('orders.status', [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED])
            ->exists();

        if ($used) {
            throw ValidationException::withMessages(['variant' => 'Variasi masih dipakai pesanan aktif.']);
        }

        $path = $variant->image_path;
        $variant->delete();
        $images->delete($path);

        app(AuditLogService::class)->record('variant.deleted', $variant, ['name' => $variant->name], null, $request->user('admin'));

        return back()->with('success', 'Variasi dihapus.');
    }
}
