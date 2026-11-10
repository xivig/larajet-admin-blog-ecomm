<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'billing_address',
        'billing_city',
        'billing_country',
        'notes',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'currency',
        'coupon_code',
        'payment_gateway',
        'payment_status',
        'order_status',
        'transaction_id',
        'payment_payload',
        'paid_at',
        'ip_address',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $code = 'XIV-' . date('Y') . '-' . str_pad((string) random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::withTrashed()->where('order_number', $code)->exists());

        return $code;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
