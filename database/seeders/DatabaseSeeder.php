<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Services\ProductService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->firstOrCreate(
            ['email' => 'admin@decakhandmade.test'],
            [
                'name' => 'Admin Decak',
                'password' => Hash::make('DecakAdmin2026!'),
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        $settings = [
            'store_name' => 'Decak Handmade',
            'store_phone' => '0812-3456-7890',
            'store_address' => 'Jl. Kerajinan No. 10, Yogyakarta',
            'store_note' => 'Produk handmade, dibuat dengan cinta.',
            'order_prefix' => 'ORD',
            'low_stock_threshold' => '3',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'store']);
        }

        $categories = collect([
            'Rajut' => 'Produk rajutan tangan',
            'Aksesori' => 'Aksesori pelengkap',
            'Dekorasi Rumah' => 'Dekorasi untuk rumah',
        ])->map(fn (string $note, string $name) => Category::query()->firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'is_active' => true],
        ));

        $service = app(ProductService::class);

        $products = [
            [
                'category' => 'Rajut',
                'name' => 'Tas Rajut Ayu',
                'sku' => 'DH-BAG-001',
                'description' => 'Tas rajut tangan dengan tali panjang, cocok untuk aktivitas harian.',
                'base_price' => 185000,
                'stock' => 12,
                'min_stock' => 3,
                'variants' => [
                    ['name' => 'Natural', 'type' => 'color', 'color_hex' => '#D9C7A7', 'price_adjustment' => 0],
                    ['name' => 'Olive', 'type' => 'color', 'color_hex' => '#6B7F5A', 'price_adjustment' => 0],
                    ['name' => 'Motif Bunga', 'type' => 'motif', 'price_adjustment' => 15000],
                ],
                'options' => [
                    ['name' => 'Nama Pemilik', 'input_type' => 'text', 'price' => 15000, 'max_length' => 20, 'is_required' => false],
                    ['name' => 'Kartu Ucapan', 'input_type' => 'text', 'price' => 5000, 'max_length' => 100, 'is_required' => false],
                ],
            ],
            [
                'category' => 'Rajut',
                'name' => 'Dompet Rajut Mini',
                'sku' => 'DH-WLT-001',
                'description' => 'Dompet rajut mini dengan kancing kayu.',
                'base_price' => 75000,
                'stock' => 20,
                'min_stock' => 5,
                'variants' => [
                    ['name' => 'Krem', 'type' => 'color', 'color_hex' => '#F3E9D2', 'price_adjustment' => 0],
                    ['name' => 'Terracotta', 'type' => 'color', 'color_hex' => '#C96F4A', 'price_adjustment' => 5000],
                ],
                'options' => [
                    ['name' => 'Inisial', 'input_type' => 'text', 'price' => 10000, 'max_length' => 5, 'is_required' => false],
                ],
            ],
            [
                'category' => 'Dekorasi Rumah',
                'name' => 'Hiasan Dinding Macrame',
                'sku' => 'DH-DEC-001',
                'description' => 'Hiasan dinding macrame dengan panjang 60 cm.',
                'base_price' => 220000,
                'stock' => 6,
                'min_stock' => 2,
                'variants' => [
                    ['name' => 'Natural', 'type' => 'color', 'color_hex' => '#E8DCC8', 'price_adjustment' => 0],
                ],
                'options' => [
                    ['name' => 'Warna Tali', 'input_type' => 'select', 'price' => 0, 'max_length' => null, 'is_required' => false],
                ],
            ],
        ];

        foreach ($products as $row) {
            if (Product::withTrashed()->where('sku', $row['sku'])->exists()) {
                continue;
            }

            $product = $service->create([
                'category_id' => $categories[$row['category']]->id,
                'name' => $row['name'],
                'sku' => $row['sku'],
                'description' => $row['description'],
                'base_price' => $row['base_price'],
                'stock' => $row['stock'],
                'min_stock' => $row['min_stock'],
                'is_active' => true,
            ], $admin);

            foreach ($row['variants'] as $index => $variant) {
                $product->variants()->create($variant + ['sort_order' => $index, 'is_active' => true]);
            }

            foreach ($row['options'] as $index => $option) {
                $product->customizationOptions()->create($option + ['sort_order' => $index, 'is_active' => true]);
            }
        }
    }
}
