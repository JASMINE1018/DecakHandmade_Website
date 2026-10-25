<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:admins,email'],
            'password' => ['required', 'string', 'confirmed', Password::min(12)->letters()->mixedCase()->numbers()],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
