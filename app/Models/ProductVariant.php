<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'type',
        'color_hex',
        'image_path',
        'sku',
        'price_adjustment',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_adjustment' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
