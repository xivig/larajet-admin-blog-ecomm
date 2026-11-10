<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'iso3',
        'flag_emoji',
        'visitors_count',
    ];

    protected function casts(): array
    {
        return [
            'visitors_count' => 'integer',
        ];
    }
}
