<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.products.index', ['tab' => 'categories']);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        $category = Category::create($data);

        app(AuditLogService::class)->record('category.created', $category, null, $category->only(['name']), $request->user('admin'));

        return back()->with('success', 'Kategori ditambahkan.');
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($category->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        app(AuditLogService::class)->record('category.updated', $category, null, $category->only(['name', 'is_active']), $request->user('admin'));

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        if (Product::withTrashed()->where('category_id', $category->id)->exists()) {
            throw ValidationException::withMessages(['category' => 'Kategori masih memiliki produk.']);
        }

        $category->delete();

        app(AuditLogService::class)->record('category.deleted', $category, ['name' => $category->name], null, $request->user('admin'));

        return back()->with('success', 'Kategori dihapus.');
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'kategori';
        $slug = $base;
        $i = 2;

        while (Category::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
