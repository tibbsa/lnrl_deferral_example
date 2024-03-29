<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeferredNotification extends Model
{
    protected $fillable = [
        'key',
        'notify_at',
        'notification_payload',
        'notifiable_payload',
    ];
}
