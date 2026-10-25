<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductCustomizationOption;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PricingService
{
    /**
     * Hitung kutipan harga dari payload pesanan. Harga tidak pernah diterima dari klien.
     *
     * @param  array<int, array{product_id:int, variant_id?:int|null, quantity:int, customizations?:array, note?:string|null}>  $items
     * @return array{items: array<int, array<string, mixed>>, subtotal: int}
     */
    public function quote(array $items): array
    {
        $lines = [];
        $subtotal = 0;

        foreach ($items as $index => $row) {
            $product = Product::query()->active()->find($row['product_id'] ?? null);

            if (! $product) {
                throw ValidationException::withMessages([
                    "items.$index.product_id" => 'Produk tidak ditemukan atau tidak aktif.',
                ]);
            }

            $variant = null;
            if (! empty($row['variant_id'])) {
                $variant = ProductVariant::query()
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->find($row['variant_id']);

                if (! $variant) {
                    throw ValidationException::withMessages([
                        "items.$index.variant_id" => 'Variasi tidak sesuai dengan produk.',
                    ]);
                }
            }

            $customizations = $this->resolveCustomizations($product, $row['customizations'] ?? [], $index);
            $quantity = max(1, (int) ($row['quantity'] ?? 1));

            $unitPrice = (int) $product->base_price
                + (int) ($variant?->price_adjustment ?? 0)
                + $customizations->sum(fn (array $c) => $c['price']);

            $lineTotal = $unitPrice * $quantity;
            $subtotal += $lineTotal;

            $lines[] = [
                'product' => $product,
                'variant' => $variant,
                'customizations' => $customizations,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'note' => $row['note'] ?? null,
            ];
        }

        return ['items' => $lines, 'subtotal' => $subtotal];
    }

    private function resolveCustomizations(Product $product, array $rows, int $index): Collection
    {
        $resolved = collect();
        $textOptions = 0;
        $seenOptions = [];

        foreach ($rows as $row) {
            if (empty($row['option_id'])) {
                continue;
            }

            if (isset($seenOptions[(int) $row['option_id']])) {
                throw ValidationException::withMessages([
                    "items.$index.customizations" => 'Opsi personalisasi tidak boleh dipilih lebih dari sekali.',
                ]);
            }

            $seenOptions[(int) $row['option_id']] = true;

            /** @var ProductCustomizationOption|null $option */
            $option = ProductCustomizationOption::query()
                ->where('product_id', $product->id)
                ->where('is_active', true)
                ->find($row['option_id']);

            if (! $option) {
                throw ValidationException::withMessages([
                    "items.$index.customizations" => 'Opsi personalisasi tidak sesuai dengan produk.',
                ]);
            }

            $value = trim((string) ($row['value'] ?? ''));

            if ($value === '' && $option->is_required) {
                throw ValidationException::withMessages([
                    "items.$index.customizations" => "Opsi {$option->name} wajib diisi.",
                ]);
            }

            if ($value === '') {
                continue;
            }

            if ($option->max_length && mb_strlen($value) > $option->max_length) {
                throw ValidationException::withMessages([
                    "items.$index.customizations" => "Teks {$option->name} maksimal {$option->max_length} karakter.",
                ]);
            }

            if ($option->input_type === 'text') {
                $textOptions++;
                if ($textOptions > 1) {
                    throw ValidationException::withMessages([
                        "items.$index.customizations" => 'Satu item hanya boleh memakai satu opsi teks personalisasi.',
                    ]);
                }
            }

            $resolved->push([
                'option' => $option,
                'option_name' => $option->name,
                'input_type' => $option->input_type,
                'value' => $value,
                'price' => (int) $option->price,
            ]);
        }

        return $resolved;
    }
}
