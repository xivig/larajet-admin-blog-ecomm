<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'session_id',
        'user_agent',
        'device',
        'browser',
        'platform',
        'country',
        'country_code',
        'city',
        'hits_count',
        'first_visited_at',
        'last_visited_at',
    ];

    protected function casts(): array
    {
        return [
            'hits_count' => 'integer',
            'first_visited_at' => 'datetime',
            'last_visited_at' => 'datetime',
        ];
    }

    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class);
    }

    public function postViews(): HasMany
    {
        return $this->hasMany(PostView::class);
    }
}
