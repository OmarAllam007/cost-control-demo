<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreResource extends Model
{
    protected $guarded = [];

    function actual_resource()
    {
        return $this->belongsTo(ActualResources::class);
    }
}
