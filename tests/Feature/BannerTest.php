<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Banner;
use App\Services\ImageStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

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
    }

    public function test_banner_can_be_created_with_webp_image(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.banners.store'), [
            'title' => 'Promo Rajut',
            'text' => 'Diskon 10% minggu ini',
            'link_url' => '/produk',
            'is_active' => 1,
            'image' => UploadedFile::fake()->image('banner.jpg', 1800, 700),
        ]);

        $response->assertRedirect(route('admin.banners.index'));

        $banner = Banner::firstOrFail();
        $this->assertSame('Promo Rajut', $banner->title);
        $this->assertStringEndsWith('.webp', $banner->image_path);
        Storage::disk('public')->assertExists($banner->image_path);

        [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($banner->image_path));
        $this->assertSame(1600, $width);
        $this->assertSame(533, $height);
    }

    public function test_invalid_link_is_rejected(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.banners.create'))
            ->post(route('admin.banners.store'), [
                'title' => 'Promo',
                'link_url' => 'javascript:alert(1)',
                'is_active' => 1,
                'image' => UploadedFile::fake()->image('banner.jpg', 1800, 700),
            ]);

        $response->assertSessionHasErrors('link_url');
        $this->assertSame(0, Banner::count());
    }

    public function test_updating_banner_replaces_and_deletes_old_image(): void
    {
        $images = app(ImageStorageService::class);
        $oldPath = $images->storeBannerImage(UploadedFile::fake()->image('old.jpg', 1800, 700));

        $banner = Banner::create([
            'title' => 'Promo Lama',
            'image_path' => $oldPath,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($this->admin, 'admin')->put(route('admin.banners.update', $banner), [
            'title' => 'Promo Baru',
            'is_active' => 1,
            'image' => UploadedFile::fake()->image('new.jpg', 1800, 700),
        ])->assertRedirect(route('admin.banners.index'));

        $banner->refresh();

        $this->assertSame('Promo Baru', $banner->title);
        $this->assertNotSame($oldPath, $banner->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($banner->image_path);
    }

    public function test_deleting_banner_removes_image(): void
    {
        $images = app(ImageStorageService::class);
        $path = $images->storeBannerImage(UploadedFile::fake()->image('banner.jpg', 1800, 700));

        $banner = Banner::create([
            'title' => 'Promo',
            'image_path' => $path,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.banners.destroy', $banner))
            ->assertRedirect(route('admin.banners.index'));

        $this->assertSame(0, Banner::count());
        Storage::disk('public')->assertMissing($path);
    }
}
