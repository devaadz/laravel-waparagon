<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $fillable = ['form_id', 'store_id', 'email'];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function answers()
    {
        return $this->hasMany(ResponseAnswer::class);
    }
    
}
