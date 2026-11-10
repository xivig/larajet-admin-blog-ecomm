<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'ad_type',
        'image_path',
        'target_url',
        'script_code',
        'impressions_count',
        'clicks_count',
        'status',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'impressions_count' => 'integer',
            'clicks_count' => 'integer',
            'status' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
