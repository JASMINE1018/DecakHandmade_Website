<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('admin.banners.index', [
            'banners' => Banner::query()->orderByDesc('sort_order')->latest('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.form', ['banner' => new Banner(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(StoreBannerRequest $request, BannerService $service): RedirectResponse
    {
        $service->create($request->validated(), $request->file('image'), $request->user('admin'));

        return redirect()->route('admin.banners.index')->with('success', 'Banner dibuat.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.form', ['banner' => $banner]);
    }

    public function update(UpdateBannerRequest $request, Banner $banner, BannerService $service): RedirectResponse
    {
        $service->update($banner, $request->validated(), $request->file('image'), $request->user('admin'));

        return redirect()->route('admin.banners.index')->with('success', 'Banner diperbarui.');
    }

    public function destroy(Request $request, Banner $banner, BannerService $service): RedirectResponse
    {
        $service->delete($banner, $request->user('admin'));

        return redirect()->route('admin.banners.index')->with('success', 'Banner dihapus.');
    }

    public function toggle(Request $request, Banner $banner): RedirectResponse
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        app(\App\Services\AuditLogService::class)->record('banner.status_changed', $banner, null, ['is_active' => $banner->is_active], $request->user('admin'));

        return back()->with('success', 'Status banner diperbarui.');
    }
}
