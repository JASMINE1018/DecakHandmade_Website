<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login user
     */
    public function showLogin()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('user.dashboard');
        }

        return view('dashboard-user.auth.login');
    }

    /**
     * Proses login user
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('user.dashboard'));
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
    }

    /**
     * Tampilkan halaman registrasi user
     */
    public function showRegister()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('user.dashboard');
        }

        return view('dashboard-user.auth.register');
    }

    /**
     * Proses pendaftaran akun user baru
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:customers,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan login.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Gunakan minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui Terms & Privacy Policy.',
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'phone' => null,
            'address' => null,
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard')->with('success', 'Selamat datang di Decak Handmade! Akun Anda berhasil dibuat.');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }
}
