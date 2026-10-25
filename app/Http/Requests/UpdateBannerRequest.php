<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'text' => ['nullable', 'string', 'max:300'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'link_url' => ['nullable', 'string', 'max:255', $this->linkRule()],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function linkRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! $value) {
                return;
            }

            if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
                return;
            }

            $scheme = parse_url($value, PHP_URL_SCHEME);

            if (! filter_var($value, FILTER_VALIDATE_URL) || ! in_array($scheme, ['http', 'https'], true)) {
                $fail('Tautan harus berupa URL http/https yang valid atau path internal yang diawali /.');
            }
        };
    }
}
