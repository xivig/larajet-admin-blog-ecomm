<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedirectRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_path',
        'target_path',
        'status_code',
        'is_active',
        'hits_count',
        'last_accessed_at',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'status_code' => 'integer',
            'hits_count' => 'integer',
            'last_accessed_at' => 'datetime',
        ];
    }
}
