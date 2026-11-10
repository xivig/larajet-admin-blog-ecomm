<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver',
        'name',
        'subtitle',
        'icon',
        'is_enabled',
        'mode',
        'config',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'array',
        'sort_order' => 'integer',
    ];

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true)->orderBy('sort_order');
    }

    /**
     * Retrieve a config field cleanly.
     */
    public function configValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->config ?? [], $key, $default);
    }

    /**
     * Check whether real external API credentials (not blank/masked placeholder) are saved.
     */
    public function hasLiveApiCredentials(): bool
    {
        $cfg = $this->config ?? [];

        return match ($this->driver) {
            'stripe' => !empty($cfg['secret_key']) && !str_contains((string) $cfg['secret_key'], '••')
                && (str_starts_with((string) $cfg['secret_key'], 'sk_test_') || str_starts_with((string) $cfg['secret_key'], 'sk_live_')),
            'razorpay' => !empty($cfg['key_id']) && !empty($cfg['key_secret']) && !str_contains((string) $cfg['key_secret'], '••'),
            'paypal' => !empty($cfg['client_id']) && !empty($cfg['client_secret']) && !str_contains((string) $cfg['client_secret'], '••'),
            default => true,
        };
    }

    /**
     * Safe masked string for Admin UI password inputs.
     */
    public static function maskSecret(?string $value): string
    {
        if (empty($value)) {
            return '';
        }
        $len = strlen($value);
        if ($len <= 8) {
            return str_repeat('•', $len);
        }
        return substr($value, 0, 5) . str_repeat('•', 12) . substr($value, -4);
    }
}
