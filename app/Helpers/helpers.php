<?php

use App\Models\Setting;
use Illuminate\Support\Carbon;

if (!function_exists('cms_setting')) {
    /**
     * Retrieve a dynamic site setting value from Database Cache.
     */
    function cms_setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('active_timezone')) {
    /**
     * Get the active system timezone identifier.
     */
    function active_timezone(): string
    {
        return config('app.timezone', 'UTC');
    }
}

if (!function_exists('cms_now')) {
    /**
     * Get Carbon now in the active system timezone.
     */
    function cms_now(): Carbon
    {
        return Carbon::now(active_timezone());
    }
}

if (!function_exists('format_bytes')) {
    /**
     * Convert bytes into human-readable formatted string.
     */
    function format_bytes(int $bytes, int $precision = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Determine if site maintenance mode is currently active.
     */
    function is_maintenance_mode(): bool
    {
        return Setting::get('site_maintenance') === '1';
    }
}
