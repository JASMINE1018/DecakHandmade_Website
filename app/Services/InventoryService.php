<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function initial(Product $product, int $quantity, ?Admin $actor = null, ?string $note = null): InventoryMovement
    {
        return $this->writeMovement($product, 'initial', $quantity, 'initial', $actor, $note);
    }

    public function adjust(Product $product, string $mode, int $quantity, string $reason, ?string $note = null, ?Admin $actor = null): InventoryMovement
    {
        $type = match ($mode) {
            'in' => 'in',
            'out' => 'out',
            'set' => 'adjustment',
            default => throw ValidationException::withMessages(['mode' => 'Jenis penyesuaian tidak dikenal.']),
        };

        $delta = match ($mode) {
            'in' => $quantity,
            'out' => -$quantity,
            'set' => null,
        };

        return DB::transaction(function () use ($product, $type, $mode, $delta, $quantity, $reason, $note, $actor) {
            /** @var Product $locked */
            $locked = Product::query()->lockForUpdate()->findOrFail($product->id);

            $before = $locked->stock;
            $after = $mode === 'set' ? $quantity : $before + $delta;

            if ($after < 0) {
                throw ValidationException::withMessages(['quantity' => 'Stok tidak boleh negatif.']);
            }

            $locked->forceFill(['stock' => $after])->save();

            return $this->createMovement($locked, $type, $after - $before, $before, $after, $reason, null, null, $note, $actor);
        });
    }

    /**
     * Kurangi stok untuk seluruh item pesanan. Idempoten per pesanan/produk.
     */
    public function deductForOrder(Order $order, Admin $actor): void
    {
        $this->applyForOrder($order, 'sale', 'order', $actor, false);
    }

    /**
     * Kembalikan stok pesanan. Idempoten.
     */
    public function restoreForOrder(Order $order, Admin $actor, string $reason = 'cancel'): void
    {
        $this->applyForOrder($order, 'return', $reason, $actor, true);
    }

    private function applyForOrder(Order $order, string $type, string $reason, Admin $actor, bool $restore): void
    {
        $quantities = $order->items()
            ->selectRaw('product_id, SUM(quantity) as total')
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->pluck('total', 'product_id');

        if ($quantities->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($order, $quantities, $type, $reason, $actor, $restore) {
            $products = Product::query()
                ->whereIn('id', $quantities->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($quantities as $productId => $quantity) {
                $product = $products->get($productId);
                if (! $product) {
                    continue;
                }

                $exists = InventoryMovement::query()
                    ->where('reference_type', 'order')
                    ->where('reference_id', $order->id)
                    ->where('product_id', $product->id)
                    ->where('type', $type)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $quantity = (int) $quantity;
                $delta = $restore ? $quantity : -$quantity;

                if (! $restore && $product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi (tersedia {$product->stock}, diminta {$quantity}).",
                    ]);
                }

                $before = $product->stock;
                $after = $before + $delta;

                $product->forceFill(['stock' => $after])->save();

                $this->createMovement($product, $type, $delta, $before, $after, $reason, 'order', $order->id, null, $actor);
            }
        });
    }

    private function writeMovement(Product $product, string $type, int $quantity, string $reason, ?Admin $actor, ?string $note): InventoryMovement
    {
        return DB::transaction(function () use ($product, $type, $quantity, $reason, $actor, $note) {
            /** @var Product $locked */
            $locked = Product::query()->lockForUpdate()->findOrFail($product->id);
            $before = $locked->stock;
            $after = $before + $quantity;

            if ($after < 0) {
                throw ValidationException::withMessages(['quantity' => 'Stok tidak boleh negatif.']);
            }

            $locked->forceFill(['stock' => $after])->save();

            return $this->createMovement($locked, $type, $quantity, $before, $after, $reason, null, null, $note, $actor);
        });
    }

    private function createMovement(
        Product $product,
        string $type,
        int $delta,
        int $before,
        int $after,
        string $reason,
        ?string $referenceType,
        ?int $referenceId,
        ?string $note,
        ?Admin $actor,
    ): InventoryMovement {
        return InventoryMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $delta,
            'stock_before' => $before,
            'stock_after' => $after,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'created_by' => $actor?->getKey(),
            'created_at' => now(),
        ]);
    }
}
