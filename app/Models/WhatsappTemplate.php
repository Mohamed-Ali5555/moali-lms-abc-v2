<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $fillable = [
        'event_key',
        'title',
        'body',
        'send_to_student',
        'send_to_parent',
        'is_active',
        'placeholders_hint',
    ];

    protected $casts = [
        'send_to_student' => 'boolean',
        'send_to_parent' => 'boolean',
        'is_active' => 'boolean',
    ];
}
