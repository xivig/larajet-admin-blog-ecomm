<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'uses_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function validateForSubtotal(float $subtotal): ?string
    {
        if (!$this->is_active) {
            return 'This promo code is currently inactive.';
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'This promo code has expired.';
        }
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return 'This promo code has reached its usage limit.';
        }
        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return 'Minimum order subtotal of $' . number_format((float) $this->min_order_amount, 2) . ' is required.';
        }
        return null;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->validateForSubtotal($subtotal) !== null) {
            return 0.0;
        }
        if ($this->type === 'percent') {
            $discount = $subtotal * ((float) $this->value / 100);
        } else {
            $discount = (float) $this->value;
        }
        return round(min($subtotal, max(0.0, $discount)), 2);
    }
}
