<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode' => ['required', 'in:in,out,set'],
            'quantity' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'in:purchase,return,damage,correction,other'],
            'note' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }
}
