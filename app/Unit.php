<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';
    protected $fillable = ['type','code'];

    protected $dates = ['created_at', 'updated_at'];

    static function options()
    {
        return static::pluck('type', 'id')->prepend('Select Unit', '');
    }
}