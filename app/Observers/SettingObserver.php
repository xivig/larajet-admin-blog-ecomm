<?php

namespace App\Observers;

use App\Events\SettingsUpdated;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    /**
     * Handle the Setting "saved" event.
     */
    public function saved(Setting $setting): void
    {
        Cache::forget('site_settings_all');
        Cache::forget(Setting::CACHE_PREFIX . $setting->key);
        event(new SettingsUpdated($setting->group ?? 'general', [$setting->key => $setting->value]));
    }

    /**
     * Handle the Setting "deleted" event.
     */
    public function deleted(Setting $setting): void
    {
        Cache::forget('site_settings_all');
        Cache::forget(Setting::CACHE_PREFIX . $setting->key);
        event(new SettingsUpdated($setting->group ?? 'general', [$setting->key => null]));
    }
}
