<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_mode' => ['required', 'in:existing,new'],
            'customer_id' => ['nullable', 'required_if:customer_mode,existing', 'exists:customers,id'],
            'customer_name' => ['nullable', 'required_if:customer_mode,new', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'required_if:customer_mode,new', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'customer_address' => ['nullable', 'string', 'max:500'],

            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.note' => ['nullable', 'string', 'max:300'],
            'items.*.customizations' => ['nullable', 'array'],
            'items.*.customizations.*.option_id' => ['required', 'integer', 'exists:product_customization_options,id'],
            'items.*.customizations.*.value' => ['nullable', 'string', 'max:200'],

            'shipping_cost' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'customer_note' => ['nullable', 'string', 'max:500'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
