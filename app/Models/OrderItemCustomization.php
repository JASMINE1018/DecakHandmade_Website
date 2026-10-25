<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemCustomization extends Model
{
    public $timestamps = false;

    protected $fillable = ['order_item_id', 'option_id', 'option_name', 'input_type', 'value', 'price'];

    protected function casts(): array
    {
        return ['price' => 'integer'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
