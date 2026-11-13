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
            'Hadiah & Souvenir' => 'Paket hadiah dan souvenir handmade',
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
            [
                'category' => 'Rajut',
                'name' => 'Cardigan Rajut Hangat',
                'sku' => 'DH-CRD-001',
                'description' => 'Cardigan rajut tangan berbahan benang katun lembut, nyaman dipakai harian.',
                'base_price' => 320000,
                'stock' => 8,
                'min_stock' => 2,
                'variants' => [
                    ['name' => 'Oatmeal', 'type' => 'color', 'color_hex' => '#E5DEC9', 'price_adjustment' => 0],
                    ['name' => 'Sage Green', 'type' => 'color', 'color_hex' => '#8FA382', 'price_adjustment' => 10000],
                    ['name' => 'Dusty Rose', 'type' => 'color', 'color_hex' => '#C98A8B', 'price_adjustment' => 10000],
                ],
                'options' => [
                    ['name' => 'Ukuran (S/M/L/XL)', 'input_type' => 'text', 'price' => 0, 'max_length' => 10, 'is_required' => true],
                    ['name' => 'Bordir Inisial', 'input_type' => 'text', 'price' => 15000, 'max_length' => 5, 'is_required' => false],
                ],
            ],
            [
                'category' => 'Rajut',
                'name' => 'Bucket Hat Rajut',
                'sku' => 'DH-HAT-001',
                'description' => 'Topi bucket rajut ringan dengan motif anyaman rapi untuk aktivitas luar ruang.',
                'base_price' => 95000,
                'stock' => 15,
                'min_stock' => 4,
                'variants' => [
                    ['name' => 'Mustard', 'type' => 'color', 'color_hex' => '#D4A037', 'price_adjustment' => 0],
                    ['name' => 'Navy', 'type' => 'color', 'color_hex' => '#2B3A55', 'price_adjustment' => 0],
                    ['name' => 'Motif Kotak', 'type' => 'motif', 'price_adjustment' => 15000],
                ],
                'options' => [
                    ['name' => 'Lingkar Kepala (cm)', 'input_type' => 'text', 'price' => 0, 'max_length' => 10, 'is_required' => false],
                ],
            ],
            [
                'category' => 'Aksesori',
                'name' => 'Gantungan Kunci Amigurumi',
                'sku' => 'DH-ACC-001',
                'description' => 'Gantungan kunci boneka rajut amigurumi lucu buatan tangan.',
                'base_price' => 35000,
                'stock' => 30,
                'min_stock' => 5,
                'variants' => [
                    ['name' => 'Beruang Cokelat', 'type' => 'motif', 'price_adjustment' => 0],
                    ['name' => 'Kelinci Putih', 'type' => 'motif', 'price_adjustment' => 0],
                    ['name' => 'Kucing Oranye', 'type' => 'motif', 'price_adjustment' => 5000],
                ],
                'options' => [
                    ['name' => 'Tag Nama', 'input_type' => 'text', 'price' => 5000, 'max_length' => 12, 'is_required' => false],
                ],
            ],
            [
                'category' => 'Aksesori',
                'name' => 'Bros Bunga Rajut',
                'sku' => 'DH-ACC-002',
                'description' => 'Bros bunga rajut dengan peniti antikarat, cocok untuk hijab dan tas.',
                'base_price' => 28000,
                'stock' => 25,
                'min_stock' => 5,
                'variants' => [
                    ['name' => 'Maroon', 'type' => 'color', 'color_hex' => '#7A2E3A', 'price_adjustment' => 0],
                    ['name' => 'Lavender', 'type' => 'color', 'color_hex' => '#B39EB5', 'price_adjustment' => 0],
                    ['name' => 'Sunflower', 'type' => 'color', 'color_hex' => '#E6B422', 'price_adjustment' => 3000],
                ],
                'options' => [
                    ['name' => 'Kartu Ucapan', 'input_type' => 'text', 'price' => 5000, 'max_length' => 80, 'is_required' => false],
                ],
            ],
            [
                'category' => 'Aksesori',
                'name' => 'Scrunchie Rajut Premium',
                'sku' => 'DH-ACC-003',
                'description' => 'Ikat rambut rajut elastis berbahan benang milk cotton yang lembut di rambut.',
                'base_price' => 22000,
                'stock' => 40,
                'min_stock' => 8,
                'variants' => [
                    ['name' => 'Matcha', 'type' => 'color', 'color_hex' => '#9CB071', 'price_adjustment' => 0],
                    ['name' => 'Mocha', 'type' => 'color', 'color_hex' => '#8B6D5C', 'price_adjustment' => 0],
                    ['name' => 'Lilac', 'type' => 'color', 'color_hex' => '#C8A2C8', 'price_adjustment' => 0],
                ],
                'options' => [],
            ],
            [
                'category' => 'Dekorasi Rumah',
                'name' => 'Coaster Tatakan Gelas Macrame (Set 4)',
                'sku' => 'DH-DEC-002',
                'description' => 'Set 4 tatakan gelas macrame berdiameter 11 cm dengan rumbai rapi.',
                'base_price' => 68000,
                'stock' => 14,
                'min_stock' => 3,
                'variants' => [
                    ['name' => 'Broken White', 'type' => 'color', 'color_hex' => '#F5F1E6', 'price_adjustment' => 0],
                    ['name' => 'Caramel', 'type' => 'color', 'color_hex' => '#B87A4B', 'price_adjustment' => 5000],
                ],
                'options' => [
                    ['name' => 'Kemasan Kotak Kado', 'input_type' => 'select', 'price' => 12000, 'max_length' => null, 'is_required' => false],
                ],
            ],
            [
                'category' => 'Hadiah & Souvenir',
                'name' => 'Hampers Rajut Gift Box',
                'sku' => 'DH-GFT-001',
                'description' => 'Paket kado berisi dompet rajut mini, gantungan kunci amigurumi, dan kartu ucapan.',
                'base_price' => 145000,
                'stock' => 2,
                'min_stock' => 3,
                'variants' => [
                    ['name' => 'Tema Hangat (Krem & Cokelat)', 'type' => 'motif', 'price_adjustment' => 0],
                    ['name' => 'Tema Pastel (Lilac & Krem)', 'type' => 'motif', 'price_adjustment' => 10000],
                ],
                'options' => [
                    ['name' => 'Isi Kartu Ucapan', 'input_type' => 'text', 'price' => 0, 'max_length' => 150, 'is_required' => true],
                    ['name' => 'Nama Penerima di Pita', 'input_type' => 'text', 'price' => 10000, 'max_length' => 25, 'is_required' => false],
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
