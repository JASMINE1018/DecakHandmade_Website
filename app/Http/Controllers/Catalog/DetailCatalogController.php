<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\View\View;

class DetailCatalogController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::query()
            ->active()
            ->with([
                'category',
                'variants' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'customizationOptions' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            ])
            ->where('slug', $slug)
            ->first();

        $productPayload = null;

        if ($product) {
            $variants = $product->variants->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'swatch' => $v->color_hex ?: '#D9C7A7',
                'border' => '#C6B08C',
                'price_adjustment' => (int) $v->price_adjustment,
            ])->values()->all();

            if ($variants === []) {
                $variants = [[
                    'id' => null,
                    'name' => 'Default',
                    'swatch' => '#E4D3B8',
                    'border' => '#C6B08C',
                    'price_adjustment' => 0,
                ]];
            }

            $galleryItems = collect($product->gallery ?? [])
                ->map(fn ($path, $idx) => [
                    'src' => str_starts_with($path, 'http') || str_starts_with($path, '/')
                        ? ltrim($path, '/')
                        : 'storage/'.ltrim($path, '/'),
                    'alt' => "{$product->name} - view ".($idx + 1),
                    'label' => 'View '.($idx + 1),
                ])
                ->values()
                ->all();

            if ($galleryItems === []) {
                $galleryItems = [[
                    'src' => 'images/products/tote-senja-natural-front.svg',
                    'alt' => $product->name,
                    'label' => 'Front view',
                ]];
            }

            $galleryByVariant = [];
            foreach ($variants as $variant) {
                $galleryByVariant[$variant['name']] = $galleryItems;
            }

            $phone = preg_replace('/\D+/', '', (string) Setting::get('store_phone', '081234567890')) ?? '6281234567890';
            if (str_starts_with($phone, '0')) {
                $phone = '62'.substr($phone, 1);
            }

            $customer = auth('customer')->user();
            $isWishlisted = $customer ? $customer->wishlists()->where('product_id', $product->id)->exists() : false;

            $productPayload = [
                'id' => $product->id,
                'sku' => strtoupper(($product->category?->name ?? 'HANDMADE').' - '.$product->sku),
                'name' => $product->name,
                'price' => (int) $product->base_price,
                'stock' => (int) $product->stock,
                'description' => $product->description ?: 'Artisan handcrafted piece made with natural materials.',
                'material' => 'Selected handcrafted materials',
                'dimensions' => 'Standard Decak Handmade size',
                'colors' => $variants,
                'gallery' => $galleryByVariant,
                'whatsappNumber' => $phone,
                'isWishlisted' => $isWishlisted,
            ];
        }

        return view('catalog.catalog-product.show', [
            'slug' => $slug,
            'productPayload' => $productPayload,
        ]);
    }
}
