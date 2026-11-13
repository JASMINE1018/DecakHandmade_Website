<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartService
{
    private const SESSION_KEY = 'cart.items';

    public function __construct(
        private readonly PricingService $pricing,
    ) {}

    /**
     * Ambil ringkasan keranjang belanja beserta harga aktual dari server.
     *
     * @return array{
     *     items: array<int, array<string, mixed>>,
     *     subtotal: int,
     *     total_quantity: int,
     *     count: int
     * }
     */
    public function summary(): array
    {
        $raw = Session::get(self::SESSION_KEY, []);

        if ($raw === []) {
            return [
                'items' => [],
                'subtotal' => 0,
                'total_quantity' => 0,
                'count' => 0,
            ];
        }

        $items = [];
        $validRaw = [];

        foreach ($raw as $key => $entry) {
            try {
                $quote = $this->pricing->quote([$entry]);
            } catch (ValidationException) {
                continue;
            }

            $line = $quote['items'][0];
            /** @var Product $product */
            $product = $line['product'];

            if ($product->stock <= 0) {
                continue;
            }

            $quantity = min($line['quantity'], $product->stock);
            $unitPrice = $line['unit_price'];
            $lineTotal = $unitPrice * $quantity;

            $entry['quantity'] = $quantity;
            $validRaw[$key] = $entry;

            $firstImage = null;
            if (is_array($product->gallery) && count($product->gallery) > 0) {
                $path = $product->gallery[0];
                $firstImage = str_starts_with($path, 'http') || str_starts_with($path, '/')
                    ? $path
                    : '/storage/'.ltrim($path, '/');
            }

            $items[] = [
                'key' => $key,
                'product_id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'sku' => $product->sku,
                'image' => $firstImage ?? '/images/products/tote-senja.png',
                'variant_id' => $line['variant']?->id,
                'variant_name' => $line['variant']?->name,
                'customizations' => collect($line['customizations'])->map(fn (array $c) => [
                    'option_id' => $c['option']->id,
                    'option_name' => $c['option_name'],
                    'value' => $c['value'],
                    'price' => $c['price'],
                ])->values()->all(),
                'note' => $line['note'],
                'quantity' => $quantity,
                'max_stock' => $product->stock,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        }

        if (count($validRaw) !== count($raw)) {
            Session::put(self::SESSION_KEY, $validRaw);
        }

        $subtotal = array_sum(array_column($items, 'line_total'));
        $totalQuantity = array_sum(array_column($items, 'quantity'));

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'total_quantity' => $totalQuantity,
            'count' => count($items),
        ];
    }

    /**
     * Tambahkan produk ke keranjang sesi.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function addItem(array $payload): array
    {
        $entry = [
            'product_id' => (int) ($payload['product_id'] ?? 0),
            'variant_id' => ! empty($payload['variant_id']) ? (int) $payload['variant_id'] : null,
            'quantity' => max(1, (int) ($payload['quantity'] ?? 1)),
            'customizations' => $this->normalizeCustomizations($payload['customizations'] ?? []),
            'note' => isset($payload['note']) ? trim((string) $payload['note']) ?: null : null,
        ];

        $quote = $this->pricing->quote([$entry]);
        /** @var Product $product */
        $product = $quote['items'][0]['product'];

        $raw = Session::get(self::SESSION_KEY, []);
        $key = $this->makeKey($entry);

        $existingQty = isset($raw[$key]) ? (int) $raw[$key]['quantity'] : 0;
        $nextQty = $existingQty + $entry['quantity'];

        if ($product->stock <= 0) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} sedang habis.",
            ]);
        }

        if ($nextQty > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} hanya tersisa {$product->stock} item.",
            ]);
        }

        $entry['quantity'] = $nextQty;
        $raw[$key] = $entry;

        Session::put(self::SESSION_KEY, $raw);

        return $this->summary();
    }

    /**
     * Perbarui kuantitas item di keranjang.
     *
     * @return array<string, mixed>
     */
    public function updateQuantity(string $key, int $quantity): array
    {
        $raw = Session::get(self::SESSION_KEY, []);

        if (! isset($raw[$key])) {
            throw ValidationException::withMessages([
                'item' => 'Item keranjang tidak ditemukan.',
            ]);
        }

        if ($quantity <= 0) {
            unset($raw[$key]);
            Session::put(self::SESSION_KEY, $raw);

            return $this->summary();
        }

        $entry = $raw[$key];
        $product = Product::query()->active()->find($entry['product_id']);

        if (! $product || $product->stock <= 0) {
            unset($raw[$key]);
            Session::put(self::SESSION_KEY, $raw);

            return $this->summary();
        }

        if ($quantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Maksimal pembelian untuk {$product->name} adalah {$product->stock} item.",
            ]);
        }

        $raw[$key]['quantity'] = $quantity;
        Session::put(self::SESSION_KEY, $raw);

        return $this->summary();
    }

    /**
     * Hapus satu item dari keranjang.
     *
     * @return array<string, mixed>
     */
    public function removeItem(string $key): array
    {
        $raw = Session::get(self::SESSION_KEY, []);
        unset($raw[$key]);
        Session::put(self::SESSION_KEY, $raw);

        return $this->summary();
    }

    /**
     * Kosongkan seluruh isi keranjang.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rawItems(): array
    {
        return array_values(Session::get(self::SESSION_KEY, []));
    }

    /**
     * @param  array<int, array<string, mixed>>  $customizations
     * @return array<int, array{option_id: int, value: string}>
     */
    private function normalizeCustomizations(array $customizations): array
    {
        return collect($customizations)
            ->filter(fn ($row) => ! empty($row['option_id']))
            ->map(fn ($row) => [
                'option_id' => (int) $row['option_id'],
                'value' => trim((string) ($row['value'] ?? '')),
            ])
            ->sortBy('option_id')
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function makeKey(array $entry): string
    {
        return md5(json_encode([
            'product_id' => $entry['product_id'],
            'variant_id' => $entry['variant_id'],
            'customizations' => $entry['customizations'],
            'note' => $entry['note'],
        ]));
    }
}
