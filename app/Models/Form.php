<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Form extends Model
{
    use HasUuids;

protected $fillable = [
        'name', 
        'slug',
        'description', 
        'status', 
        'enable_email_notification',
        'email_subject',
        'email_template',
        'enable_whatsapp_notification',
        'whatsapp_template',
        'enable_whatsapp_image',
        'whatsapp_image',
        'whatsapp_template_as_caption',
        'privacy_policy',
        'language'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'enable_email_notification' => 'boolean',
'enable_whatsapp_notification' => 'boolean',
        'enable_whatsapp_image' => 'boolean',
        'whatsapp_template_as_caption' => 'boolean',
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function formStores()
    {
        return $this->hasMany(FormStore::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'form_stores');
    }

    public function notification_logs()
    {
        return $this->hasMany(NotificationLog::class);
    }

    // Get store-specific slug (helper)
    public function getStoreSlugAttribute($storeId)
    {
        $formStore = $this->formStores()->where('store_id', $storeId)->first();
        return $formStore ? $formStore->custom_url_slug : null;
    }
}

