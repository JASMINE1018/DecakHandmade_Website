<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Services\PricingService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PricingTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Product $product;

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
    }

    public function test_price_is_computed_from_server_side_data(): void
    {
        $variant = $this->product->variants()->create([
            'name' => 'Olive',
            'type' => 'color',
            'color_hex' => '#6B7F5A',
            'price_adjustment' => 5000,
            'is_active' => true,
        ]);

        $option = $this->product->customizationOptions()->create([
            'name' => 'Nama',
            'input_type' => 'text',
            'price' => 10000,
            'max_length' => 20,
            'is_active' => true,
        ]);

        $quote = app(PricingService::class)->quote([[
            'product_id' => $this->product->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
            'customizations' => [['option_id' => $option->id, 'value' => 'Siti']],
        ]]);

        $this->assertSame(115000, $quote['items'][0]['unit_price']);
        $this->assertSame(230000, $quote['subtotal']);
    }

    public function test_two_text_personalization_options_are_rejected(): void
    {
        $first = $this->product->customizationOptions()->create([
            'name' => 'Nama',
            'input_type' => 'text',
            'price' => 10000,
            'max_length' => 20,
            'is_active' => true,
        ]);

        $second = $this->product->customizationOptions()->create([
            'name' => 'Ucapan',
            'input_type' => 'text',
            'price' => 5000,
            'max_length' => 100,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        app(PricingService::class)->quote([[
            'product_id' => $this->product->id,
            'quantity' => 1,
            'customizations' => [
                ['option_id' => $first->id, 'value' => 'Siti'],
                ['option_id' => $second->id, 'value' => 'Selamat ulang tahun'],
            ],
        ]]);
    }

    public function test_required_customization_must_be_filled(): void
    {
        $option = $this->product->customizationOptions()->create([
            'name' => 'Nama',
            'input_type' => 'text',
            'price' => 10000,
            'max_length' => 20,
            'is_required' => true,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        app(PricingService::class)->quote([[
            'product_id' => $this->product->id,
            'quantity' => 1,
            'customizations' => [['option_id' => $option->id, 'value' => '']],
        ]]);
    }

    public function test_option_from_another_product_is_rejected(): void
    {
        $other = app(ProductService::class)->create([
            'category_id' => $this->product->category_id,
            'name' => 'Dompet',
            'sku' => 'SKU-002',
            'base_price' => 50000,
            'stock' => 5,
            'min_stock' => 0,
            'is_active' => true,
        ], $this->admin);

        $foreignOption = $other->customizationOptions()->create([
            'name' => 'Nama',
            'input_type' => 'text',
            'price' => 10000,
            'max_length' => 20,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        app(PricingService::class)->quote([[
            'product_id' => $this->product->id,
            'quantity' => 1,
            'customizations' => [['option_id' => $foreignOption->id, 'value' => 'Siti']],
        ]]);
    }

    public function test_max_length_is_enforced(): void
    {
        $option = $this->product->customizationOptions()->create([
            'name' => 'Nama',
            'input_type' => 'text',
            'price' => 10000,
            'max_length' => 5,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        app(PricingService::class)->quote([[
            'product_id' => $this->product->id,
            'quantity' => 1,
            'customizations' => [['option_id' => $option->id, 'value' => 'Terlalu panjang']],
        ]]);
    }

    public function test_duplicate_customization_option_is_rejected(): void
    {
        $option = $this->product->customizationOptions()->create([
            'name' => 'Nama',
            'input_type' => 'text',
            'price' => 10000,
            'max_length' => 20,
            'is_active' => true,
        ]);

        $this->expectException(ValidationException::class);

        app(PricingService::class)->quote([[
            'product_id' => $this->product->id,
            'quantity' => 1,
            'customizations' => [
                ['option_id' => $option->id, 'value' => 'Siti'],
                ['option_id' => $option->id, 'value' => 'Aminah'],
            ],
        ]]);
    }
}
