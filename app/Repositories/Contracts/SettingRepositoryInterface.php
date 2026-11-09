<?php

namespace App\Repositories\Contracts;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

interface SettingRepositoryInterface
{
    public function all(): Collection;

    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): Setting;

    public function forget(string $key): bool;
}
