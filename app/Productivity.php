<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productivity extends Model
{
    protected $fillable = ['name'];

    protected $dates = ['created_at', 'updated_at'];
}