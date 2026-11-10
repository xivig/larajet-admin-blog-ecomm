<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotFoundLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'referer',
        'ip',
        'user_agent',
        'hits_count',
        'is_resolved',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'is_resolved' => 'boolean',
            'hits_count' => 'integer',
            'last_seen_at' => 'datetime',
        ];
    }
}
