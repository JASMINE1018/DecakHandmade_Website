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
            'recipient_name' => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z\s\.,\'-]+$/'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{8,20}$/'],
            'full_address' => ['required', 'string', 'max:500'],
            'province' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.,\'-]+$/'],
            'city' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.,\'-]+$/'],
            'subdistrict' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['required', 'digits:5'],
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
            'recipient_name.regex' => 'Nama penerima hanya boleh berisi huruf dan tanda baca nama.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid.',
            'full_address.required' => 'Alamat lengkap wajib diisi.',
            'province.required' => 'Provinsi wajib diisi.',
            'province.regex' => 'Nama provinsi hanya boleh berisi huruf.',
            'city.required' => 'Kota / kabupaten wajib diisi.',
            'city.regex' => 'Nama kota / kabupaten hanya boleh berisi huruf.',
            'postal_code.required' => 'Kode pos wajib diisi.',
            'postal_code.digits' => 'Kode pos harus berupa 5 digit angka.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ];
    }
}
