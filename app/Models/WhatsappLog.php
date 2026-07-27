<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $fillable = [
        'event_key',
        'user_id',
        'recipient_type',
        'phone',
        'message',
        'status',
        'response',
    ];
}
