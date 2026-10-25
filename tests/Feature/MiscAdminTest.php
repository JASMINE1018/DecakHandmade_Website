<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MiscAdminTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('SecretPassword123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_order_notes_and_shipping_can_be_updated_before_completion(): void
    {
        $category = Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);
        $product = app(ProductService::class)->create([
            'category_id' => $category->id,
            'name' => 'Tas Rajut',
            'sku' => 'SKU-001',
            'base_price' => 100000,
            'stock' => 10,
            'min_stock' => 0,
            'is_active' => true,
        ], $this->admin);
        $customer = Customer::create(['name' => 'Siti', 'phone' => '081234567890']);

        $order = app(OrderService::class)->create([
            'customer_mode' => 'existing',
            'customer_id' => $customer->id,
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
            'shipping_cost' => 10000,
        ], $this->admin);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.update', $order), [
                'shipping_cost' => 25000,
                'customer_note' => 'Kirim ke kantor',
                'admin_note' => 'Sudah dikonfirmasi',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $order->refresh();

        $this->assertSame(25000, $order->shipping_cost);
        $this->assertSame(225000, $order->grand_total);
        $this->assertSame('Kirim ke kantor', $order->customer_note);
    }

    public function test_settings_can_be_updated(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.settings.update'), [
                'store_name' => 'Decak Handmade Studio',
                'store_phone' => '081200000000',
                'store_address' => 'Yogyakarta',
                'order_prefix' => 'ORD',
                'low_stock_threshold' => 5,
            ])
            ->assertRedirect();

        $this->assertSame('Decak Handmade Studio', Setting::get('store_name'));
        $this->assertSame('5', Setting::get('low_stock_threshold'));
    }

    public function test_admin_can_be_created_and_updated(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.admins.store'), [
            'name' => 'Admin Kedua',
            'email' => 'second@example.test',
            'password' => 'SecondPassword123',
            'password_confirmation' => 'SecondPassword123',
            'is_active' => 1,
        ])->assertRedirect();

        $second = Admin::where('email', 'second@example.test')->firstOrFail();

        $this->actingAs($this->admin, 'admin')->put(route('admin.admins.update', $second), [
            'name' => 'Admin Kedua Diperbarui',
            'email' => 'second@example.test',
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertSame('Admin Kedua Diperbarui', $second->fresh()->name);
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.admins.index'))
            ->delete(route('admin.admins.destroy', $this->admin))
            ->assertSessionHasErrors('admin');

        $this->assertTrue($this->admin->fresh()->is_active);
    }

    public function test_order_cannot_be_updated_after_completion(): void
    {
        $category = Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);
        $product = app(ProductService::class)->create([
            'category_id' => $category->id,
            'name' => 'Tas Rajut',
            'sku' => 'SKU-002',
            'base_price' => 100000,
            'stock' => 10,
            'min_stock' => 0,
            'is_active' => true,
        ], $this->admin);
        $customer = Customer::create(['name' => 'Siti', 'phone' => '081234567891']);

        $order = app(OrderService::class)->create([
            'customer_mode' => 'existing',
            'customer_id' => $customer->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'shipping_cost' => 0,
        ], $this->admin);

        app(OrderService::class)->transition($order, Order::STATUS_SHIPPED, null, $this->admin);
        app(OrderService::class)->transition($order->fresh(), Order::STATUS_COMPLETED, null, $this->admin);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.orders.show', $order))
            ->put(route('admin.orders.update', $order), ['admin_note' => 'Terlambat'])
            ->assertSessionHasErrors('order');
    }
}
