<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Cache key prefix for individual settings.
     */
    public const CACHE_PREFIX = 'site_setting_';
    public const ALL_CACHE_KEY = 'site_settings_all';

    public static function get(string $key, $default = null): ?string
    {
        try {
            return Cache::remember(self::CACHE_PREFIX . $key, 86400, function () use ($key, $default) {
                $setting = static::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (\Throwable $e) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        }
    }

    public static function set(string $key, $value, string $group = 'general'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        // Invalidate Database Cache
        try {
            Cache::forget(self::CACHE_PREFIX . $key);
            Cache::forget(self::ALL_CACHE_KEY);
        } catch (\Throwable $e) {
            // Ignore if cache store unavailable during migration
        }

        return $setting;
    }

    public static function getAll(): Collection
    {
        try {
            return Cache::remember(self::ALL_CACHE_KEY, 86400, function () {
                return static::all()->pluck('value', 'key');
            });
        } catch (\Throwable $e) {
            return static::all()->pluck('value', 'key');
        }
    }

    public static function clearCache(): void
    {
        try {
            Cache::forget(self::ALL_CACHE_KEY);
            foreach (static::pluck('key') as $key) {
                Cache::forget(self::CACHE_PREFIX . $key);
            }
        } catch (\Throwable $e) {}
    }
}
