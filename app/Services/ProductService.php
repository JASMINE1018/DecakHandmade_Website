<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function __construct(
        private readonly InventoryService $inventory,
        private readonly AuditLogService $audit,
    ) {}

    public function create(array $data, Admin $admin): Product
    {
        $product = Product::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'sku' => $data['sku'],
            'description' => $data['description'] ?? null,
            'base_price' => (int) $data['base_price'],
            'stock' => 0,
            'min_stock' => (int) ($data['min_stock'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $stock = max(0, (int) ($data['stock'] ?? 0));
        $this->inventory->initial($product, $stock, $admin, 'Stok awal produk');

        $this->audit->record('product.created', $product, null, $product->only(['name', 'sku', 'base_price', 'stock']), $admin);

        return $product->fresh();
    }

    public function update(Product $product, array $data, Admin $admin): Product
    {
        $token = $data['updated_at_token'] ?? null;

        if ($token && $token !== $product->updated_at?->toIso8601String()) {
            throw ValidationException::withMessages([
                'updated_at_token' => 'Data produk sudah berubah oleh admin lain. Muat ulang halaman sebelum menyimpan.',
            ]);
        }

        $before = $product->only(['name', 'sku', 'base_price', 'min_stock', 'is_active', 'category_id']);

        $product->fill([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'sku' => $data['sku'],
            'description' => $data['description'] ?? null,
            'base_price' => (int) $data['base_price'],
            'min_stock' => (int) ($data['min_stock'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        if ($product->isDirty('name')) {
            $product->slug = $this->uniqueSlug($data['name'], $product->id);
        }

        $product->save();

        $this->audit->record('product.updated', $product, $before, $product->only(array_keys($before)), $admin);

        return $product;
    }

    public function archive(Product $product, Admin $admin): void
    {
        $inActiveOrder = Order::query()
            ->whereIn('status', [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED])
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();

        if ($inActiveOrder) {
            throw ValidationException::withMessages([
                'product' => 'Produk masih dipakai pada pesanan Diproses atau Dikirim sehingga tidak dapat diarsipkan.',
            ]);
        }

        $before = $product->only(['name', 'sku', 'deleted_at']);
        $product->delete();

        $this->audit->record('product.archived', $product, $before, ['deleted_at' => now()], $admin);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $i = 2;

        while (Product::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
