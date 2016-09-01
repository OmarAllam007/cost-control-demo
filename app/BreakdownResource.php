<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BreakdownResource extends Model
{
    protected $fillable = ['breakdown_id', 'std_activity_resource_id', 'wbs_level', 'budget_qty', 'eng_qty', 'resource_waste', 'labor_count', 'remarks', 'productivity_id', 'remarks','code'];

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
        $V = $this->budget_qty;
        $result = '';
        eval('$result=' . $this->resource->equation.';');
        return $result;
    }

    function getBudgetUnitAttribute()
    {
        if ($this->productivity) {
            if (!$this->productivity->reduction_factor) {
                return 0;
            }

            $result = $this->resource_qty * $this->labor_count / $this->productivity->reduction_factor;
            return $result > 0.25 ? round($result, 2) : 0.25;
        } else {
            return $this->resource_qty * (1 + $this->resource_waste);
        }
    }

    function getBudgetCostAttribute()
    {
        return $this->budget_unit * $this->resource->resource->rate;
    }

    function getBoqUnitRateAttribute()
    {
        if (!$this->eng_qty) {
            return 0;
        }
        return $this->budget_cost / $this->eng_qty;
    }
}
