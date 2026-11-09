<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SettingRepository implements SettingRepositoryInterface
{
    public function all(): Collection
    {
        return Setting::all();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    public function set(string $key, mixed $value): Setting
    {
        return Setting::set($key, $value);
    }

    public function forget(string $key): bool
    {
        return Setting::forget($key);
    }
}
