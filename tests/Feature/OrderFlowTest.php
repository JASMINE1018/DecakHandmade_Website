<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCustomizationOption;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Category $category;

    private Product $product;

    private ProductVariant $variant;

    private ProductCustomizationOption $option;

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

        $this->category = Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);

        $this->product = app(ProductService::class)->create([
            'category_id' => $this->category->id,
            'name' => 'Tas Rajut',
            'sku' => 'SKU-001',
            'base_price' => 100000,
            'stock' => 10,
            'min_stock' => 2,
            'is_active' => true,
        ], $this->admin);

        $this->variant = $this->product->variants()->create([
            'name' => 'Olive',
            'type' => 'color',
            'color_hex' => '#6B7F5A',
            'price_adjustment' => 5000,
            'is_active' => true,
        ]);

        $this->option = $this->product->customizationOptions()->create([
            'name' => 'Nama Pemilik',
            'input_type' => 'text',
            'price' => 10000,
            'max_length' => 20,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'name' => 'Siti',
            'phone' => '081234567890',
            'address' => 'Sleman',
        ]);
    }

    public function test_order_creation_snapshots_prices_and_deducts_stock(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.orders.store'), [
            'customer_mode' => 'existing',
            'customer_id' => $this->customer->id,
            'items' => [[
                'product_id' => $this->product->id,
                'variant_id' => $this->variant->id,
                'quantity' => 2,
                'customizations' => [
                    ['option_id' => $this->option->id, 'value' => 'Siti'],
                ],
            ]],
            'shipping_cost' => 10000,
        ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('admin.orders.show', $order));

        $this->assertSame('ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT), $order->order_number);
        $this->assertSame(Order::STATUS_PROCESSING, $order->status);
        $this->assertSame(230000, $order->subtotal);
        $this->assertSame(10000, $order->shipping_cost);
        $this->assertSame(240000, $order->grand_total);
        $this->assertSame(8, $this->product->fresh()->stock);

        $item = $order->items()->firstOrFail();
        $this->assertSame(115000, $item->unit_price);
        $this->assertSame(230000, $item->line_total);
        $this->assertSame('Tas Rajut', $item->product_name);
        $this->assertSame(1, $item->variants()->count());
        $this->assertSame('Siti', $item->customizations()->firstOrFail()->value);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'type' => 'sale',
            'quantity' => -2,
            'stock_before' => 10,
            'stock_after' => 8,
            'reference_type' => 'order',
            'reference_id' => $order->id,
        ]);
    }

    public function test_status_flow_marks_completed_at(): void
    {
        $order = $this->createOrder(1);

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.status.update', $order), ['status' => 'shipped', 'note' => 'Dikirim'])
            ->assertRedirect();

        $this->assertSame(Order::STATUS_SHIPPED, $order->fresh()->status);
        $this->assertNull($order->fresh()->completed_at);

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.status.update', $order), ['status' => 'completed'])
            ->assertRedirect();

        $this->assertSame(Order::STATUS_COMPLETED, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->completed_at);
    }

    public function test_cancelling_processing_order_restores_stock(): void
    {
        $order = $this->createOrder(3);

        $this->assertSame(7, $this->product->fresh()->stock);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.orders.cancel', $order), [
                'reason' => 'customer_request',
                'note' => 'Pelanggan membatalkan pesanan',
            ])
            ->assertRedirect();

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(10, $this->product->fresh()->stock);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'type' => 'return',
            'quantity' => 3,
            'reference_type' => 'order',
            'reference_id' => $order->id,
        ]);
    }

    public function test_cancelling_shipped_order_requires_return_confirmation(): void
    {
        $order = $this->createOrder(2);

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.status.update', $order), ['status' => 'shipped'])
            ->assertRedirect();

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.orders.cancel', $order), [
                'reason' => 'customer_request',
                'note' => 'Pelanggan menolak paket',
            ])
            ->assertSessionHasErrors('returned');

        $this->assertSame(Order::STATUS_SHIPPED, $order->fresh()->status);
        $this->assertSame(8, $this->product->fresh()->stock);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.orders.cancel', $order), [
                'reason' => 'customer_request',
                'note' => 'Pelanggan menolak paket',
                'returned' => 1,
            ])
            ->assertRedirect();

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(10, $this->product->fresh()->stock);
    }

    public function test_completed_order_cannot_change_status(): void
    {
        $order = $this->createOrder(1);

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.status.update', $order), ['status' => 'shipped']);

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.status.update', $order), ['status' => 'completed']);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.orders.show', $order))
            ->patch(route('admin.orders.status.update', $order), ['status' => 'processing'])
            ->assertSessionHasErrors('status');

        $this->assertSame(Order::STATUS_COMPLETED, $order->fresh()->status);
    }

    public function test_order_number_uses_configured_prefix(): void
    {
        Setting::create(['key' => 'order_prefix', 'value' => 'DHP', 'group' => 'store']);

        $order = $this->createOrder(1);

        $this->assertStringStartsWith('DHP-', $order->order_number);
    }

    public function test_completed_or_cancelled_order_can_be_deleted(): void
    {
        $order = $this->createOrder(1);

        // Cannot delete while in processing
        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.orders.destroy', $order))
            ->assertSessionHasErrors('order');

        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        // Complete order
        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.status.update', $order), ['status' => 'completed']);

        // Now delete should succeed
        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.orders.destroy', $order))
            ->assertRedirect(route('admin.orders.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->id]);
        $this->assertDatabaseMissing('order_status_histories', ['order_id' => $order->id]);
    }

    public function test_courier_and_tracking_number_can_be_set_when_status_updated_to_shipped(): void
    {
        $order = $this->createOrder(1);

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.status.update', $order), [
                'status' => 'shipped',
                'courier' => 'JNE Express',
                'tracking_number' => 'JNE1234567890',
            ])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertSame(Order::STATUS_SHIPPED, $order->status);
        $this->assertSame('JNE Express', $order->courier);
        $this->assertSame('JNE1234567890', $order->tracking_number);
    }

    public function test_admin_can_mark_order_as_paid(): void
    {
        $order = $this->createOrder(1);
        $this->assertFalse($order->isPaid());

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.payment.update', $order))
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertTrue($order->isPaid());
        $this->assertSame('settlement', $order->payment_status);
        $this->assertSame('Lunas', $order->paymentStatusLabel());
    }

    public function test_order_can_be_cancelled_with_reason(): void
    {
        $order = $this->createOrder(1);

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.orders.status.update', $order), [
                'status' => 'cancelled',
                'reason' => 'out_of_stock',
                'note' => 'Bahan habis di gudang',
            ])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('success');

        $order->refresh();

        $this->assertSame(Order::STATUS_CANCELLED, $order->status);
        $latestHistory = $order->statusHistories()->first();
        $this->assertStringContainsString('Stok tidak tersedia', $latestHistory->note);
        $this->assertStringContainsString('Bahan habis di gudang', $latestHistory->note);
    }

    private function createOrder(int $quantity): Order
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.orders.store'), [
            'customer_mode' => 'existing',
            'customer_id' => $this->customer->id,
            'items' => [[
                'product_id' => $this->product->id,
                'quantity' => $quantity,
            ]],
            'shipping_cost' => 0,
        ])->assertRedirect();

        return Order::latest('id')->firstOrFail();
    }
}
