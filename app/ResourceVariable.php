<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceVariable extends Model
{
    protected $fillable = ['label', 'display_order', 'std_activity_resource_id'];

    function resource()
    {
        return $this->belongsTo(StdActivityResource::class, 'std_activity_resource_id');
    }
}
