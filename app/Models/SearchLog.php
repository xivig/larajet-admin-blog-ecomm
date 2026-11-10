<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'results_count',
        'hits_count',
        'ip',
        'last_searched_at',
    ];

    protected function casts(): array
    {
        return [
            'results_count' => 'integer',
            'hits_count' => 'integer',
            'last_searched_at' => 'datetime',
        ];
    }
}
