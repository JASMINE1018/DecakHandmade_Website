<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:120'],
            'store_phone' => ['nullable', 'string', 'max:30'],
            'store_address' => ['nullable', 'string', 'max:500'],
            'store_note' => ['nullable', 'string', 'max:500'],
            'order_prefix' => ['nullable', 'alpha_num', 'min:2', 'max:6'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ];
    }
}
