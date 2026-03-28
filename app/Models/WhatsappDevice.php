<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappDevice extends Model
{
    protected $table = 'whatsapp_devices';

    protected $fillable = [
        'name',
        'device_id',
        'phone_number',
        'status',
        'is_logged_in',
        'last_login_at',
        'last_logout_at',
        'device_info',
    ];

    protected $casts = [
        'is_logged_in' => 'boolean',
        'device_info' => 'array',
        'last_login_at' => 'datetime',
        'last_logout_at' => 'datetime',
    ];

    /**
     * Get the store associated with this device
     */
    public function store()
    {
        return $this->hasOne(Store::class, 'whatsapp_device_id', 'device_id');
    }

    /**
     * Get all messages for this device
     */
    public function messages()
    {
        return $this->hasMany(WhatsappMessage::class, 'device_id', 'device_id');
    }

    /**
     * Scope for connected devices
     */
    public function scopeConnected($query)
    {
        return $query->where('status', 'connected');
    }

    /**
     * Scope for logged in devices
     */
    public function scopeLoggedIn($query)
    {
        return $query->where('is_logged_in', true);
    }
}
