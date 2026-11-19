<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:'.implode(',', array_keys(Order::STATUSES))],
            'note' => ['nullable', 'string', 'max:500'],
            'returned' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'in:customer_request,out_of_stock,mistake,other'],
            'courier' => ['nullable', 'string', 'max:60'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ];
    }
}
