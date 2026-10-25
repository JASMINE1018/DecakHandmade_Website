<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_cost' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'customer_note' => ['nullable', 'string', 'max:500'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
