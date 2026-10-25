<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.admins.index', [
            'admins' => Admin::query()->orderBy('name')->paginate(20),
        ]);
    }

    public function store(StoreAdminRequest $request, AdminService $service): RedirectResponse
    {
        $service->create($request->validated(), $request->user('admin'));

        return back()->with('success', 'Admin ditambahkan.');
    }

    public function update(UpdateAdminRequest $request, Admin $admin, AdminService $service): RedirectResponse
    {
        $service->update($admin, $request->validated(), $request->user('admin'));

        return back()->with('success', 'Admin diperbarui.');
    }

    public function deactivate(Request $request, Admin $admin, AdminService $service): RedirectResponse
    {
        $service->deactivate($admin, $request->user('admin'));

        return back()->with('success', 'Admin dinonaktifkan.');
    }
}
