<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = ['name', 'start_date'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }
}
