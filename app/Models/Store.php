<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = ['name', 'address', 'phone_number', 'whatsapp_device_id'];

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function forms()
    {
        return $this->belongsToMany(Form::class, 'form_stores')
            ->withPivot('custom_url_slug')
            ->withTimestamps();
    }

    public function formStores()
    {
        return $this->hasMany(FormStore::class);
    }
    public function whatsappDevice()
    {
        return $this->belongsTo(
            WhatsappDevice::class,
            'whatsapp_device_id', // FK di stores
            'device_id'           // PK di whatsapp_devices
        );
    }
}
