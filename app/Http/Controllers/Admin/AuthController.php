<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(LoginRequest $request, AuthService $auth): RedirectResponse
    {
        $auth->attempt(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
            $request->boolean('remember'),
        );

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(AuthService $auth): RedirectResponse
    {
        $auth->logout();

        return redirect()->route('admin.login');
    }
}
