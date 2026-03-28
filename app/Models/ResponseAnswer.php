<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponseAnswer extends Model
{
    protected $fillable = ['response_id', 'field_id', 'value'];

    public function response()
    {
        return $this->belongsTo(Response::class);
    }

    public function field()
    {
        return $this->belongsTo(FormField::class, 'field_id');
    }
}
