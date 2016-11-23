<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActualResources extends Model
{
    protected $fillable = ['breakdown_resource_id', 'period_id', 'original_code', 'qty', 'unit_price', 'cost', 'unit_id', 'action_date'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function wbs()
    {
        return $this->$this->belongsTo(WbsLevel::class, 'wbs_level_id');
    }

    function breakdown_resource()
    {
        return $this->$this->belongsTo(BreakdownResource::class, 'breakdown_resource_id');
    }

    function resource_shadow()
    {
        return $this->$this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id');
    }

    function period_id()
    {
        return $this->$this->belongsTo(Period::class);
    }

    function unit()
    {
        return $this->$this->belongsTo(Unit::class);
    }
}
