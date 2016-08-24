<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BreakdownResource extends Model
{
    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'budget_qty', 'eng_qty', 'labors_count', 'productivity_id', 'remarks', 'resource_waste'];

    function breakdown()
    {
        return $this->belongsTo(Breakdown::class);
    }

    function resource()
    {
        return $this->belongsTo(StdActivityResource::class);
    }

    function productivity()
    {
        return $this->belongsTo(Productivity::class);
    }
}
