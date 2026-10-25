<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('product_variants', 'name')->where('product_id', $this->route('product')?->id),
            ],
            'type' => ['required', 'in:color,motif'],
            'color_hex' => ['nullable', 'required_if:type,color', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'required_if:type,motif', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:product_variants,sku'],
            'price_adjustment' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
