<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductArchiveTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Product $product;

    private Customer $customer;

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

        $category = Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);

        $this->product = app(ProductService::class)->create([
            'category_id' => $category->id,
            'name' => 'Tas Rajut',
            'sku' => 'SKU-001',
            'base_price' => 100000,
            'stock' => 10,
            'min_stock' => 0,
            'is_active' => true,
        ], $this->admin);

        $this->customer = Customer::create(['name' => 'Siti', 'phone' => '081234567890']);
    }

    public function test_product_with_active_order_cannot_be_archived(): void
    {
        $order = app(OrderService::class)->create([
            'customer_mode' => 'existing',
            'customer_id' => $this->customer->id,
            'items' => [['product_id' => $this->product->id, 'quantity' => 1]],
            'shipping_cost' => 0,
        ], $this->admin);

        $this->expectException(ValidationException::class);

        app(ProductService::class)->archive($this->product, $this->admin);
    }

    public function test_product_can_be_archived_after_order_cancelled(): void
    {
        $order = app(OrderService::class)->create([
            'customer_mode' => 'existing',
            'customer_id' => $this->customer->id,
            'items' => [['product_id' => $this->product->id, 'quantity' => 1]],
            'shipping_cost' => 0,
        ], $this->admin);

        app(OrderService::class)->cancel($order, 'customer_request', 'Pelanggan membatalkan pesanan', $this->admin);

        app(ProductService::class)->archive($this->product, $this->admin);

        $this->assertSoftDeleted('products', ['id' => $this->product->id]);
    }

    public function test_stale_updated_at_token_is_rejected(): void
    {
        $this->expectException(ValidationException::class);

        app(ProductService::class)->update($this->product, [
            'category_id' => $this->product->category_id,
            'name' => 'Nama Baru',
            'sku' => $this->product->sku,
            'base_price' => 100000,
            'min_stock' => 0,
            'is_active' => true,
            'updated_at_token' => '2000-01-01T00:00:00+00:00',
        ], $this->admin);
    }

    public function test_product_can_be_updated_with_current_token(): void
    {
        $updated = app(ProductService::class)->update($this->product, [
            'category_id' => $this->product->category_id,
            'name' => 'Tas Rajut Baru',
            'sku' => $this->product->sku,
            'base_price' => 120000,
            'min_stock' => 1,
            'is_active' => true,
            'updated_at_token' => $this->product->updated_at->toIso8601String(),
        ], $this->admin);

        $this->assertSame('Tas Rajut Baru', $updated->name);
        $this->assertSame(120000, $updated->base_price);
        $this->assertNotSame('tas-rajut', $updated->slug);
    }
}
