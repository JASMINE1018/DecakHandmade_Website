<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(SettingService $settings): View
    {
        return view('admin.settings.edit', ['settings' => $settings->all()]);
    }

    public function update(UpdateSettingRequest $request, SettingService $settings): RedirectResponse
    {
        $settings->update($request->validated(), $request->user('admin'));

        return back()->with('success', 'Pengaturan disimpan.');
    }
}
