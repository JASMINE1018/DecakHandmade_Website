<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', ['admin' => $request->user('admin')]);
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = $request->user('admin');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:admins,email,'.$admin->id],
        ]);

        $admin->update($data);

        app(\App\Services\AuditLogService::class)->record('profile.updated', $admin, null, $admin->only(['name', 'email']), $admin);

        return back()->with('success', 'Profil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()],
        ]);

        $admin = $request->user('admin');
        $admin->update(['password' => Hash::make($data['password'])]);

        app(\App\Services\AuditLogService::class)->record('profile.password_changed', $admin, null, null, $admin);

        return back()->with('success', 'Kata sandi diperbarui.');
    }
}
