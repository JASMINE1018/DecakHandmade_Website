<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'recipient_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'full_address' => ['required', 'string', 'max:500'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'subdistrict' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:15'],
            'payment_method' => ['required', 'in:bank,ewallet'],
            'customer_note' => ['nullable', 'string', 'max:300'],
            'save_as_primary' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'recipient_name.required' => 'Nama penerima wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'full_address.required' => 'Alamat lengkap wajib diisi.',
            'province.required' => 'Provinsi wajib diisi.',
            'city.required' => 'Kota / kabupaten wajib diisi.',
            'postal_code.required' => 'Kode pos wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ];
    }
}
