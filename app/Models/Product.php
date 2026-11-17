<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'base_price',
        'stock',
        'min_stock',
        'is_active',
        'gallery',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'integer',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'is_active' => 'boolean',
            'gallery' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function customizationOptions(): HasMany
    {
        return $this->hasMany(ProductCustomizationOption::class)->orderBy('sort_order');
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedBy(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'wishlists', 'product_id', 'customer_id')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getImageUrl(string $size = 'card'): string
    {
        $first = is_array($this->gallery) && count($this->gallery) > 0 ? $this->gallery[0] : null;

        if (! $first) {
            return asset('images/products/tote-senja.png');
        }

        if (str_starts_with($first, 'http://') || str_starts_with($first, 'https://')) {
            return $first;
        }

        if (preg_match('/\.(webp|jpg|jpeg|png|svg)$/i', $first)) {
            return asset('storage/'.ltrim($first, '/'));
        }

        return asset("storage/{$first}-{$size}.webp");
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}
