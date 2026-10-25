<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
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

    public function test_categories_are_managed_from_the_products_page(): void
    {
        $category = Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.products.index', ['tab' => 'categories']))
            ->assertOk()
            ->assertSee('Kategori Produk')
            ->assertSee('Tambah kategori')
            ->assertSee($category->name);
    }

    public function test_products_tab_still_renders_product_filters(): void
    {
        Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Tambah produk')
            ->assertSee('Semua kategori');
    }

    public function test_old_categories_url_redirects_to_the_products_category_tab(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.categories.index'))
            ->assertRedirect(route('admin.products.index', ['tab' => 'categories']));
    }

    public function test_category_can_be_created_from_the_products_page(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.products.index', ['tab' => 'categories']))
            ->post(route('admin.categories.store'), [
                'name' => 'Dekorasi Baru',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.products.index', ['tab' => 'categories']));

        $this->assertDatabaseHas('categories', [
            'name' => 'Dekorasi Baru',
            'slug' => 'dekorasi-baru',
            'is_active' => true,
        ]);
    }

    public function test_category_can_be_updated_from_the_products_page(): void
    {
        $category = Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);

        $this->actingAs($this->admin, 'admin')
            ->from(route('admin.products.index', ['tab' => 'categories']))
            ->put(route('admin.categories.update', $category), [
                'name' => 'Rajut Baru',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.products.index', ['tab' => 'categories']));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Rajut Baru',
            'slug' => 'rajut-baru',
            'is_active' => true,
        ]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::create(['name' => 'Rajut', 'slug' => 'rajut', 'is_active' => true]);

        app(ProductService::class)->create([
            'category_id' => $category->id,
            'name' => 'Tas Rajut',
            'sku' => 'SKU-001',
            'base_price' => 100000,
            'stock' => 5,
            'min_stock' => 0,
            'is_active' => true,
        ], $this->admin);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.categories.destroy', $category))
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
