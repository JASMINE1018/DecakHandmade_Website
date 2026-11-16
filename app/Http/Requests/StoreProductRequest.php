<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'description' => ['nullable', 'string', 'max:5000'],
            'base_price' => ['required', 'integer', 'min:0', 'max:2000000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:10000000'],
            'min_stock' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'is_active' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048', 'dimensions:min_width=400,min_height=400,max_width=4000,max_height=4000'],
        ];
    }

    public function messages(): array
    {
        return [
            'base_price.max' => 'Harga dasar tidak boleh melebihi Rp 2.000.000.000.',
            'stock.max' => 'Jumlah stok tidak boleh melebihi 10.000.000 unit.',
            'min_stock.max' => 'Jumlah stok minimum tidak boleh melebihi 10.000.000 unit.',
        ];
    }
}
