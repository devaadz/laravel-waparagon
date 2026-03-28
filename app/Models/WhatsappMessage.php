<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'device_id',
        'message_id',
        'from_phone',
        'to_phone',
        'text',
        'type',
        'media_url',
        'payload',
        'is_from_me',
        'message_timestamp',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_from_me' => 'boolean',
        'message_timestamp' => 'datetime',
    ];

    public function store()
    {
        return Store::where('whatsapp_device_id', $this->device_id)->first();
    }
}
