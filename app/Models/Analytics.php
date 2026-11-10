<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $table = 'analytics_metrics';

    protected $fillable = [
        'metric_key',
        'metric_value',
        'dimension',
        'dimension_value',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'metric_value' => 'integer',
            'date' => 'date',
        ];
    }
}
