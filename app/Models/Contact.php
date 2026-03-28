<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'phone_number',
        'name',
        'profile_picture',
        'is_blocked',
        'contact_info',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'contact_info' => 'array',
    ];

    /**
     * Get messages sent to this contact
     */
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'to_phone', 'phone_number');
    }

    /**
     * Get messages received from this contact
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'from_phone', 'phone_number');
    }
}
