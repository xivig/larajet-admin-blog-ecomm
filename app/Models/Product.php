<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'sku',
        'category',
        'badge_label',
        'badge_color',
        'short_description',
        'description',
        'features',
        'price',
        'sale_price',
        'currency',
        'stock_qty',
        'is_digital',
        'download_url',
        'image_url',
        'rating',
        'reviews_count',
        'sales_count',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'stock_qty' => 'integer',
        'reviews_count' => 'integer',
        'sales_count' => 'integer',
        'is_digital' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Authoritative effective price (sale_price when valid and lower than price).
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < (float) $this->price) {
            return (float) $this->sale_price;
        }
        return (float) $this->price;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < (float) $this->price;
    }

    public function isInStock(int $requestedQty = 1): bool
    {
        if ($this->stock_qty === -1) {
            return true;
        }
        return $this->stock_qty >= $requestedQty;
    }

    /**
     * Resolve resolved public image asset URL cleanly.
     */
    public function getResolvedImageUrlAttribute(): string
    {
        $raw = $this->image_url;
        if (empty($raw)) {
            return asset('xivig-blog/images/portfolio/project-nexus-core.webp');
        }
        if (Str::startsWith($raw, ['http://', 'https://', '//'])) {
            return $raw;
        }
        return asset(ltrim($raw, '/'));
    }

    public static function currencySymbol(string $currency = 'USD'): string
    {
        return match (strtoupper($currency)) {
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            'JPY' => '¥',
            default => '$',
        };
    }
}
