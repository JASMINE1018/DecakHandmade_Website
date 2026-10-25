<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\SalesReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportTest extends TestCase
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
            'stock' => 20,
            'min_stock' => 0,
            'is_active' => true,
        ], $this->admin);

        $this->customer = Customer::create(['name' => 'Siti', 'phone' => '081234567890']);
    }

    public function test_revenue_uses_subtotal_and_excludes_shipping_and_cancelled_orders(): void
    {
        $completed = $this->createOrder(2, 20000);
        $this->complete($completed);

        $cancelled = $this->createOrder(1, 5000);
        app(OrderService::class)->cancel($cancelled, 'customer_request', 'Pelanggan membatalkan pesanan', $this->admin);

        $today = now('Asia/Jakarta')->toDateString();
        $summary = app(SalesReportService::class)->summary($today, $today);

        $this->assertSame(200000, $summary['revenue']);
        $this->assertSame(1, $summary['orders']);
        $this->assertSame(2, $summary['units']);
        $this->assertSame(200000, $summary['average']);
        $this->assertSame(1, $summary['new_customers']);
    }

    public function test_reports_and_exports_are_available(): void
    {
        $order = $this->createOrder(1, 10000);
        $this->complete($order);

        $today = now('Asia/Jakarta')->toDateString();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.sales.index', ['start_date' => $today, 'end_date' => $today]))
            ->assertOk();

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.products.index', ['start_date' => $today, 'end_date' => $today]))
            ->assertOk();

        $pdf = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.export', ['format' => 'pdf', 'start_date' => $today, 'end_date' => $today]));

        $pdf->assertOk();
        $pdf->assertHeader('content-type', 'application/pdf');

        $xlsx = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.export', ['format' => 'xlsx', 'start_date' => $today, 'end_date' => $today]));

        $xlsx->assertOk();
        $xlsx->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    private function createOrder(int $quantity, int $shipping): Order
    {
        return app(OrderService::class)->create([
            'customer_mode' => 'existing',
            'customer_id' => $this->customer->id,
            'items' => [[
                'product_id' => $this->product->id,
                'quantity' => $quantity,
            ]],
            'shipping_cost' => $shipping,
        ], $this->admin);
    }

    private function complete(Order $order): void
    {
        $service = app(OrderService::class);
        $service->transition($order, Order::STATUS_SHIPPED, null, $this->admin);
        $service->transition($order->fresh(), Order::STATUS_COMPLETED, null, $this->admin);
    }
}
