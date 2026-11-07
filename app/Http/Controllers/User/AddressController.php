<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    protected function getCustomer(): Customer
    {
        return Auth::guard('customer')->user() ?? Customer::first();
    }

    public function index()
    {
        $customer = $this->getCustomer();
        $addresses = $customer->addresses()->orderByDesc('is_primary')->latest()->get();

        return view('dashboard-user.addresses', compact('customer', 'addresses'));
    }

    public function store(Request $request)
    {
        $customer = $this->getCustomer();

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:60'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:25'],
            'full_address' => ['required', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $isPrimary = $request->boolean('is_primary');

        // Jika alamat pertama, otomatis jadikan primary
        if ($customer->addresses()->count() === 0) {
            $isPrimary = true;
        }

        DB::transaction(function () use ($customer, $validated, $isPrimary) {
            if ($isPrimary) {
                $customer->addresses()->update(['is_primary' => false]);
            }

            $customer->addresses()->create([
                'label' => $validated['label'],
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'full_address' => $validated['full_address'],
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'is_primary' => $isPrimary,
            ]);
        });

        return redirect()->route('user.addresses')->with('success', 'Alamat baru berhasil ditambahkan.');
    }

    public function update(Request $request, CustomerAddress $address)
    {
        $customer = $this->getCustomer();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:60'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:25'],
            'full_address' => ['required', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $isPrimary = $request->boolean('is_primary');

        DB::transaction(function () use ($customer, $address, $validated, $isPrimary) {
            if ($isPrimary) {
                $customer->addresses()->where('id', '!=', $address->id)->update(['is_primary' => false]);
            }

            $address->update([
                'label' => $validated['label'],
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'full_address' => $validated['full_address'],
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'is_primary' => $isPrimary || $address->is_primary,
            ]);
        });

        return redirect()->route('user.addresses')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function setPrimary(CustomerAddress $address)
    {
        $customer = $this->getCustomer();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        DB::transaction(function () use ($customer, $address) {
            $customer->addresses()->update(['is_primary' => false]);
            $address->update(['is_primary' => true]);
        });

        return redirect()->route('user.addresses')->with('success', 'Alamat utama berhasil diubah.');
    }

    public function destroy(CustomerAddress $address)
    {
        $customer = $this->getCustomer();

        if ($address->customer_id !== $customer->id) {
            abort(403);
        }

        $wasPrimary = $address->is_primary;
        $address->delete();

        if ($wasPrimary) {
            $firstOther = $customer->addresses()->first();
            if ($firstOther) {
                $firstOther->update(['is_primary' => true]);
            }
        }

        return redirect()->route('user.addresses')->with('success', 'Alamat berhasil dihapus.');
    }
}
