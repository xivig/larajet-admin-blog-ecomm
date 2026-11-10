<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory;

    protected $fillable = [
        'area',
        'widget_type',
        'title',
        'content',
        'settings',
        'order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'order' => 'integer',
            'status' => 'boolean',
        ];
    }

    public static function getAreaWidgets(string $area)
    {
        return static::where('area', $area)
            ->where('status', true)
            ->orderBy('order', 'asc')
            ->get();
    }
}
