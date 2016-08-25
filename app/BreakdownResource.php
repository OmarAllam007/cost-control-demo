<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BreakdownResource extends Model
{
    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'wbs_level', 'budget_qty', 'eng_qty', 'resource_waste', 'labor_count', 'remarks', 'productivity_id', 'remarks'];

    function breakdown()
    {
        return $this->belongsTo(Breakdown::class);
    }

    function resource()
    {
        return $this->belongsTo(StdActivityResource::class, 'std_activity_resource_id');
    }

    function productivity()
    {
        return $this->belongsTo(Productivity::class);
    }

    function getResourceQtyAttribute()
    {
        $v = $this->budget_qty;
        $result = '';
        eval('$result=' . $this->resource->equation.';');

        return $result;
    }

    function getBudgetUnitAttribute()
    {

    }

    function getBudgetCostAttribute()
    {

    }
}
