<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemVariant extends Model
{
    public $timestamps = false;

    protected $fillable = ['order_item_id', 'variant_id', 'variant_name', 'price_adjustment'];

    protected function casts(): array
    {
        return ['price_adjustment' => 'integer'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
