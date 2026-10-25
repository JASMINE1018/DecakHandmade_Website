<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomizationOptionRequest;
use App\Http\Requests\UpdateCustomizationOptionRequest;
use App\Models\Product;
use App\Models\ProductCustomizationOption;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomizationOptionController extends Controller
{
    public function store(StoreCustomizationOptionRequest $request, Product $product): RedirectResponse
    {
        $option = $product->customizationOptions()->create($request->validated() + ['sort_order' => 0]);

        app(AuditLogService::class)->record('customization.created', $option, null, $option->only(['name', 'price']), $request->user('admin'));

        return back()->with('success', 'Opsi personalisasi ditambahkan.');
    }

    public function update(UpdateCustomizationOptionRequest $request, Product $product, ProductCustomizationOption $option): RedirectResponse
    {
        $option->update($request->validated());

        app(AuditLogService::class)->record('customization.updated', $option, null, $option->only(['name', 'price', 'is_active']), $request->user('admin'));

        return back()->with('success', 'Opsi personalisasi diperbarui.');
    }

    public function destroy(Request $request, Product $product, ProductCustomizationOption $option): RedirectResponse
    {
        $option->delete();

        app(AuditLogService::class)->record('customization.deleted', $option, ['name' => $option->name], null, $request->user('admin'));

        return back()->with('success', 'Opsi personalisasi dihapus.');
    }
}
