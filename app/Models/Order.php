<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PROCESSING => 'Diproses',
        self::STATUS_SHIPPED => 'In Delivery',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'source',
        'subtotal',
        'shipping_cost',
        'courier',
        'tracking_number',
        'payment_method',
        'payment_status',
        'payment_bank',
        'payment_va_number',
        'payment_qris_url',
        'payment_raw_response',
        'grand_total',
        'customer_note',
        'admin_note',
        'completed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'grand_total' => 'integer',
            'completed_at' => 'datetime',
            'payment_raw_response' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PROCESSING, self::STATUS_SHIPPED]);
    }

    public const PAYMENT_STATUSES = [
        'pending' => 'Menunggu Pembayaran',
        'settlement' => 'Lunas',
        'paid' => 'Lunas',
        'capture' => 'Lunas',
        'expire' => 'Kedaluwarsa',
        'cancel' => 'Dibatalkan',
        'failure' => 'Gagal',
    ];

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function paymentStatusLabel(): string
    {
        $status = strtolower((string) ($this->payment_status ?: 'pending'));

        return self::PAYMENT_STATUSES[$status] ?? ucfirst($status);
    }

    public function isPaid(): bool
    {
        return in_array(strtolower((string) $this->payment_status), ['settlement', 'paid', 'capture'], true);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true);
    }
}
