<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected function getCustomer(): Customer
    {
        // Jika sudah ada session auth customer gunakan itu, jika belum gunakan default customer demo
        return Auth::guard('customer')->user() ?? Customer::first();
    }

    public function index()
    {
        $customer = $this->getCustomer();
        $primaryAddress = $customer->primaryAddress ?? $customer->addresses()->first();
        $latestOrder = $customer->orders()->with(['items.product', 'statusHistories'])->latest()->first();

        return view('dashboard-user.profile', compact('customer', 'primaryAddress', 'latestOrder'));
    }

    public function update(Request $request)
    {
        $customer = $this->getCustomer();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:25'],
        ]);

        $customer->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => Customer::normalizePhone($validated['phone']),
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Informasi profil berhasil diperbarui.');
    }
}
