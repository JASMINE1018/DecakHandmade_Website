<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerUserDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::firstOrCreate(
            ['phone' => '081234567890'],
            [
                'name' => 'Ayu Pramesti',
                'email' => 'ayu.pramesti@email.com',
                'password' => Hash::make('password123'),
                'address' => 'Jl. Melati No. 18, RT 02/RW 04, Sukajadi',
                'note' => 'Pelanggan setia',
            ]
        );

        if (!$customer->password) {
            $customer->update([
                'password' => Hash::make('password123'),
                'email' => $customer->email ?? 'ayu.pramesti@email.com',
            ]);
        }

        if ($customer->addresses()->count() === 0) {
            $customer->addresses()->create([
                'label' => 'Rumah',
                'recipient_name' => 'Ayu Pramesti',
                'phone' => '0812 3456 7890',
                'full_address' => 'Jl. Melati No. 18, RT 02/RW 04, Sukajadi',
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'subdistrict' => 'Sukajadi',
                'postal_code' => '40162',
                'is_primary' => true,
            ]);

            $customer->addresses()->create([
                'label' => 'Studio / Kantor',
                'recipient_name' => 'Ayu Pramesti',
                'phone' => '0812 9876 5432',
                'full_address' => 'Jl. Riau No. 45, Citarum, Kec. Bandung Wetan',
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'subdistrict' => 'Bandung Wetan',
                'postal_code' => '40115',
                'is_primary' => false,
            ]);
        }

        if ($customer->wishlists()->count() === 0) {
            $productIds = Product::take(4)->pluck('id');
            foreach ($productIds as $pid) {
                Wishlist::firstOrCreate([
                    'customer_id' => $customer->id,
                    'product_id' => $pid,
                ]);
            }
        }

        if ($customer->orders()->count() === 0) {
            $order1 = Order::create([
                'order_number' => 'DCK-20261020-018',
                'customer_id' => $customer->id,
                'status' => 'shipped',
                'source' => 'web',
                'subtotal' => 345000,
                'shipping_cost' => 20000,
                'courier' => 'JNE Regular',
                'tracking_number' => 'JNE-98231049281',
                'grand_total' => 365000,
                'customer_note' => 'Mohon dikemas rapi ya',
                'created_by' => 1,
            ]);

            $p1 = Product::find(1);
            $p2 = Product::find(2);

            if ($p1) {
                $order1->items()->create([
                    'product_id' => $p1->id,
                    'product_name' => $p1->name,
                    'sku' => $p1->sku,
                    'unit_price' => 185000,
                    'quantity' => 1,
                    'line_total' => 185000,
                ]);
            }
            if ($p2) {
                $order1->items()->create([
                    'product_id' => $p2->id,
                    'product_name' => $p2->name,
                    'sku' => $p2->sku,
                    'unit_price' => 80000,
                    'quantity' => 2,
                    'line_total' => 160000,
                ]);
            }

            $order1->statusHistories()->createMany([
                ['from_status' => null, 'to_status' => 'processing', 'note' => 'Pembayaran berhasil diverifikasi', 'changed_by' => 1, 'created_at' => now()->subDays(2)],
                ['from_status' => 'processing', 'to_status' => 'processing', 'note' => 'Pesanan selesai dikemas di atelier Bandung', 'changed_by' => 1, 'created_at' => now()->subDays(2)->addHours(2)],
                ['from_status' => 'processing', 'to_status' => 'shipped', 'note' => 'Paket telah diserahkan ke kurir JNE', 'changed_by' => 1, 'created_at' => now()->subDay()],
                ['from_status' => 'shipped', 'to_status' => 'shipped', 'note' => 'Paket tiba di fasilitas sortir Hub Bandung', 'changed_by' => 1, 'created_at' => now()->subHours(6)],
            ]);

            $order2 = Order::create([
                'order_number' => 'DCK-20260912-004',
                'customer_id' => $customer->id,
                'status' => 'completed',
                'source' => 'web',
                'subtotal' => 185000,
                'shipping_cost' => 0,
                'courier' => 'SiCepat BEST',
                'tracking_number' => 'SCP-7492019482',
                'grand_total' => 185000,
                'customer_note' => null,
                'completed_at' => now()->subWeeks(3),
                'created_by' => 1,
            ]);

            if ($p1) {
                $order2->items()->create([
                    'product_id' => $p1->id,
                    'product_name' => $p1->name,
                    'sku' => $p1->sku,
                    'unit_price' => 185000,
                    'quantity' => 1,
                    'line_total' => 185000,
                ]);
            }

            $order2->statusHistories()->createMany([
                ['from_status' => null, 'to_status' => 'processing', 'note' => 'Pembayaran lunas via Transfer BCA', 'changed_by' => 1, 'created_at' => now()->subWeeks(3)->subDays(3)],
                ['from_status' => 'processing', 'to_status' => 'shipped', 'note' => 'Paket telah diserahkan ke kurir SiCepat', 'changed_by' => 1, 'created_at' => now()->subWeeks(3)->subDays(2)],
                ['from_status' => 'shipped', 'to_status' => 'completed', 'note' => 'Paket telah diterima dengan baik oleh pembeli', 'changed_by' => 1, 'created_at' => now()->subWeeks(3)],
            ]);
        }
    }
}
