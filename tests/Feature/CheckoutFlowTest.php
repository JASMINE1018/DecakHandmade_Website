<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_update_and_remove_cart_items_with_stock_validation(): void
    {
        $category = Category::query()->create([
            'name' => 'Rajut',
            'slug' => 'rajut',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Tas Rajut Test',
            'slug' => 'tas-rajut-test',
            'sku' => 'TEST-BAG-01',
            'base_price' => 100000,
            'stock' => 5,
            'min_stock' => 1,
            'is_active' => true,
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'name' => 'Natural',
            'type' => 'color',
            'price_adjustment' => 15000,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $addResponse = $this->postJson(route('cart.items.store'), [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $addResponse->assertCreated()
            ->assertJsonPath('subtotal', 230000)
            ->assertJsonPath('total_quantity', 2);

        $key = $addResponse->json('items.0.key');

        $overStockResponse = $this->postJson(route('cart.items.store'), [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 4,
        ]);

        $overStockResponse->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);

        $updateResponse = $this->patchJson(route('cart.items.update', ['key' => $key]), [
            'quantity' => 3,
        ]);

        $updateResponse->assertOk()
            ->assertJsonPath('subtotal', 345000)
            ->assertJsonPath('total_quantity', 3);

        $deleteResponse = $this->deleteJson(route('cart.items.destroy', ['key' => $key]));

        $deleteResponse->assertOk()
            ->assertJsonPath('count', 0)
            ->assertJsonPath('subtotal', 0);
    }

    public function test_authenticated_customer_can_checkout_and_create_order_without_shipping(): void
    {
        $customer = Customer::query()->create([
            'name' => 'Ayu Pramesti',
            'email' => 'ayu@example.test',
            'phone' => '081234567890',
            'password' => bcrypt('password123'),
        ]);

        $category = Category::query()->create([
            'name' => 'Aksesori',
            'slug' => 'aksesori',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Dompet Rajut Test',
            'slug' => 'dompet-rajut-test',
            'sku' => 'TEST-WLT-01',
            'base_price' => 150000,
            'stock' => 10,
            'min_stock' => 2,
            'is_active' => true,
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'name' => 'Terracotta',
            'type' => 'color',
            'price_adjustment' => 10000,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->postJson(route('cart.items.store'), [
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
        ])->assertCreated();

        $response = $this->actingAs($customer, 'customer')
            ->postJson(route('user.checkout.store'), [
                'recipient_name' => 'Ayu Pramesti',
                'phone' => '081234567890',
                'full_address' => 'Jl. Melati No. 18',
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'postal_code' => '40162',
                'payment_method' => 'bank',
                'customer_note' => 'Bungkus rapi ya',
                'save_as_primary' => true,
            ]);

        $response->assertCreated()
            ->assertJsonPath('grand_total', 320000)
            ->assertJsonPath('status', Order::STATUS_PROCESSING);

        $orderNumber = $response->json('order_number');
        $this->assertNotEmpty($orderNumber);

        $order = Order::query()->where('order_number', $orderNumber)->firstOrFail();
        $this->assertSame('storefront', $order->source);
        $this->assertSame(0, $order->shipping_cost);
        $this->assertSame(320000, $order->grand_total);
        $this->assertNull($order->created_by);

        $this->assertSame(8, $product->fresh()->stock);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => -2,
            'reference_type' => 'order',
            'reference_id' => $order->id,
        ]);

        $this->assertDatabaseHas('customer_addresses', [
            'customer_id' => $customer->id,
            'recipient_name' => 'Ayu Pramesti',
            'is_primary' => true,
        ]);

        $this->getJson(route('cart.summary'))
            ->assertOk()
            ->assertJsonPath('count', 0);
    }
}
