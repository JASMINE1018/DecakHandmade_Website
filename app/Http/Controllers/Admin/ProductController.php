<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageStorageService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab') === 'categories' ? 'categories' : 'products';

        $products = Product::query()
            ->with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('sku', 'like', $term));
            })
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->integer('category')))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->string('status')->toString() === 'active'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'tab' => $tab,
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'categoryManager' => $tab === 'categories'
                ? Category::query()->withCount('products')->orderBy('name')->paginate(20, ['*'], 'category_page')->withQueryString()
                : null,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true, 'min_stock' => 0]),
            'categories' => Category::active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request, ProductService $service, ImageStorageService $images): RedirectResponse
    {
        $this->assertGalleryLimit($request, new Product);

        $product = $service->create($request->validated(), $request->user('admin'));

        $this->storeImages($request, $product, $images);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product): View
    {
        $product->load(['variants', 'customizationOptions', 'category']);

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::active()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product, ProductService $service, ImageStorageService $images): RedirectResponse
    {
        $this->assertGalleryLimit($request, $product);

        $service->update($product, $request->validated(), $request->user('admin'));

        $this->storeImages($request, $product, $images);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product, ProductService $service, Request $request): RedirectResponse
    {
        $service->archive($product, $request->user('admin'));

        return redirect()->route('admin.products.index')->with('success', 'Produk diarsipkan.');
    }

    public function destroyImage(Product $product, int $index, ImageStorageService $images, Request $request): RedirectResponse
    {
        $gallery = $product->gallery ?? [];

        if (isset($gallery[$index])) {
            $images->deleteProductImage($gallery[$index]);
            unset($gallery[$index]);
            $product->forceFill(['gallery' => array_values($gallery)])->save();

            app(\App\Services\AuditLogService::class)->record('product.image_deleted', $product, null, ['index' => $index], $request->user('admin'));
        }

        return back()->with('success', 'Gambar dihapus.');
    }

    private function assertGalleryLimit(Request $request, Product $product): void
    {
        $files = $request->file('images', []);
        $files = is_array($files) ? $files : ($files ? [$files] : []);

        if (count($product->gallery ?? []) + count($files) > 5) {
            throw ValidationException::withMessages([
                'images' => 'Maksimal 5 gambar per produk.',
            ]);
        }
    }

    private function storeImages(Request $request, Product $product, ImageStorageService $images): void
    {
        $files = $request->file('images', []);

        if ($files === []) {
            return;
        }

        $gallery = $product->gallery ?? [];
        $files = is_array($files) ? $files : [$files];

        if (count($gallery) + count($files) > 5) {
            throw ValidationException::withMessages([
                'images' => 'Maksimal 5 gambar per produk.',
            ]);
        }

        foreach ($files as $file) {
            $gallery[] = $images->storeProductImage($file, $product);
        }

        $product->forceFill(['gallery' => $gallery])->save();
    }
}
