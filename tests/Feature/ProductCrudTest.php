<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Services\ImageStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('SecretPassword123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->category = Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);
    }

    public function test_product_can_be_created_with_gallery_variants_options_and_stock_adjustment(): void
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), [
            'name' => 'Tas Rajut Baru',
            'category_id' => $this->category->id,
            'sku' => 'SKU-NEW',
            'base_price' => 150000,
            'stock' => 5,
            'min_stock' => 2,
            'is_active' => 1,
            'images' => [UploadedFile::fake()->image('product.jpg', 900, 900)],
        ])->assertRedirect();

        $product = Product::firstOrFail();

        $this->assertSame(5, $product->stock);
        $this->assertCount(1, $product->gallery);
        $this->assertSame(1, $product->inventoryMovements()->where('type', 'initial')->count());

        $base = $product->gallery[0];
        Storage::disk('public')->assertExists($base.'-full.webp');
        Storage::disk('public')->assertExists($base.'-card.webp');
        Storage::disk('public')->assertExists($base.'-thumb.webp');

        $this->actingAs($this->admin, 'admin')->post(route('admin.products.variants.store', $product), [
            'name' => 'Olive',
            'type' => 'color',
            'color_hex' => '#6B7F5A',
            'price_adjustment' => 5000,
            'is_active' => 1,
        ])->assertRedirect();

        $this->actingAs($this->admin, 'admin')->post(route('admin.products.customization-options.store', $product), [
            'name' => 'Nama Pemilik',
            'input_type' => 'text',
            'price' => 10000,
            'max_length' => 20,
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertSame(1, $product->variants()->count());
        $this->assertSame(1, $product->customizationOptions()->count());

        $this->actingAs($this->admin, 'admin')->post(route('admin.products.stock-adjustments.store', $product), [
            'mode' => 'in',
            'quantity' => 3,
            'reason' => 'purchase',
            'note' => 'Restock dari supplier',
        ])->assertRedirect();

        $this->assertSame(8, $product->fresh()->stock);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 3,
            'stock_before' => 5,
            'stock_after' => 8,
        ]);
    }

    public function test_product_update_and_archive_work_through_http(): void
    {
        $product = $this->createProduct();

        $this->actingAs($this->admin, 'admin')->put(route('admin.products.update', $product), [
            'name' => 'Tas Rajut Diperbarui',
            'category_id' => $this->category->id,
            'sku' => $product->sku,
            'base_price' => 175000,
            'min_stock' => 1,
            'is_active' => 1,
            'updated_at_token' => $product->updated_at->toIso8601String(),
        ])->assertRedirect();

        $this->assertSame('Tas Rajut Diperbarui', $product->fresh()->name);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_product_update_with_stale_token_is_rejected(): void
    {
        $product = $this->createProduct();

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.products.edit', $product))
            ->put(route('admin.products.update', $product), [
                'name' => 'Tas Rajut Lain',
                'category_id' => $this->category->id,
                'sku' => $product->sku,
                'base_price' => 175000,
                'min_stock' => 1,
                'is_active' => 1,
                'updated_at_token' => '2000-01-01T00:00:00+00:00',
            ])
            ->assertSessionHasErrors('updated_at_token');

        $this->assertSame('Tas Rajut', $product->fresh()->name);
    }

    public function test_product_list_can_be_filtered_by_low_stock(): void
    {
        $normalProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Produk Aman',
            'slug' => 'produk-aman',
            'sku' => 'SKU-SAFE',
            'base_price' => 50000,
            'stock' => 10,
            'min_stock' => 2,
            'is_active' => true,
        ]);

        $lowProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Produk Hampir Habis',
            'slug' => 'produk-hampir-habis',
            'sku' => 'SKU-LOW',
            'base_price' => 50000,
            'stock' => 1,
            'min_stock' => 3,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.products.index', ['stock' => 'low']))
            ->assertOk()
            ->assertSee($lowProduct->name)
            ->assertDontSee($normalProduct->name);
    }

    public function test_product_creation_defaults_min_stock_to_setting_threshold(): void
    {
        Setting::create(['key' => 'low_stock_threshold', 'value' => '5', 'group' => 'store']);

        $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), [
            'name' => 'Produk Baru Tanpa Min Stock',
            'category_id' => $this->category->id,
            'sku' => 'SKU-NOMIN',
            'base_price' => 100000,
            'stock' => 2,
            'is_active' => 1,
        ])->assertRedirect();

        $product = Product::where('sku', 'SKU-NOMIN')->firstOrFail();
        $this->assertSame(5, $product->min_stock);
    }

    private function createProduct(): Product
    {
        $this->actingAs($this->admin, 'admin')->post(route('admin.products.store'), [
            'name' => 'Tas Rajut',
            'category_id' => $this->category->id,
            'sku' => 'SKU-001',
            'base_price' => 150000,
            'stock' => 5,
            'min_stock' => 2,
            'is_active' => 1,
        ])->assertRedirect();

        return Product::latest('id')->firstOrFail();
    }
}
