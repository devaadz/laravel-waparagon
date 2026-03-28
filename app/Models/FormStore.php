<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Form;
use App\Models\Store;

class FormStore extends Model
{
    use HasUuids;

    protected $table = 'form_stores';
    protected $fillable = ['form_id', 'store_id', 'name', 'custom_url_slug'];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function getUrlAttribute()
    {
        return '/form/' . $this->custom_url_slug;
    }
}

