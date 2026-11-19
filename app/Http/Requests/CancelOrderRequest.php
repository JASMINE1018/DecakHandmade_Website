<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'in:customer_request,out_of_stock,mistake,other'],
            'note' => ['nullable', 'string', 'max:500'],
            'returned' => ['nullable', 'boolean'],
        ];
    }
}
