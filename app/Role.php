<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'description'];

    function reports()
    {
        return $this->belongsToMany(Report::class);
    }
}
