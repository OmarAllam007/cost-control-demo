<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceCode extends Model
{
    protected $fillable = ['code', 'project_id'];

    function resource()
    {
        return $this->belongsTo(Resources::class, 'resource_id');
    }
}
