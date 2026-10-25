<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private readonly AuditLogService $audit) {}

    public function attempt(string $email, string $password, bool $remember = false): Admin
    {
        $key = 'login:'.Str::lower($email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        $admin = Admin::where('email', $email)->first();

        if (! $admin || ! $admin->is_active || ! Auth::guard('admin')->attempt(['email' => $email, 'password' => $password], $remember)) {
            RateLimiter::hit($key, 60);
            $this->audit->record('auth.login_failed', $admin, null, ['email' => $email]);

            throw ValidationException::withMessages([
                'email' => 'Kredensial tidak valid.',
            ]);
        }

        RateLimiter::clear($key);

        request()->session()->regenerate();

        $admin->forceFill(['last_login_at' => now()])->save();
        $this->audit->record('auth.login', $admin, null, ['email' => $admin->email], $admin);

        return $admin;
    }

    public function logout(): void
    {
        $admin = Auth::guard('admin')->user();

        if ($admin) {
            $this->audit->record('auth.logout', $admin, null, null, $admin);
        }

        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
