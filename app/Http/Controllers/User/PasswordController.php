<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    protected function getCustomer(): Customer
    {
        return Auth::guard('customer')->user() ?? Auth::user();
    }

    public function edit()
    {
        $customer = $this->getCustomer();

        return view('dashboard-user.password', compact('customer'));
    }

    public function update(Request $request)
    {
        $customer = $this->getCustomer();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini harus diisi.',
            'password.required' => 'Kata sandi baru harus diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if ($customer->password && !Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.']);
        }

        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }
}
