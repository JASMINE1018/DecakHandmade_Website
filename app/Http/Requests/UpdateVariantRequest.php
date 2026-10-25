<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variant = $this->route('variant');
        $product = $this->route('product');

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('product_variants', 'name')
                    ->where('product_id', $product?->id)
                    ->ignore($variant?->id),
            ],
            'type' => ['required', 'in:color,motif'],
            'color_hex' => ['nullable', 'required_if:type,color', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('product_variants', 'sku')->ignore($variant?->id)],
            'price_adjustment' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
