<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_name',
        'sender_email',
        'recipient_email',
        'subject',
        'body',
        'folder',
        'is_starred',
        'is_read',
    ];

    protected $casts = [
        'is_starred' => 'boolean',
        'is_read' => 'boolean',
    ];
}
