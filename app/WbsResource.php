<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WbsResource extends Model
{
    protected $appends = [
        'previous_unit_price', 'to_date_unit_price',
        'allowable_ev_cost', 'allowable_var', 'bl_allowable_cost', 'bl_allowable_var',
        'remaining_qty', 'remaining_cost', 'remaining_unit_price',
        'completion_qty', 'completion_cost', 'completion_unit_price',
        'qty_var', 'cost_var', 'unit_price_var', 'physical_unit', 'pw_index',
        'cost_variance_to_date_due_unit_price', 'allowable_qty', 'cost_variance_remaining_due_unit_price',
        'cost_variance_completion_due_unit_price', 'cost_variance_completion_due_qty', 'cost_variance_to_date_due_qty',
    ];

    function resource()
    {
        return $this->belongsTo(BreakdownResource::class);
    }

    function shadow()
    {
        return $this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }

    function scopeJoinShadow(Builder $query)
    {
        return $query->leftJoin('break_down_resource_shadows', 'break_down_resource_shadows.breakdown_resource_id', '=', 'wbs_resources.breakdown_resource_id')
            ->select('break_down_resource_shadows.*', 'wbs_resources.*')
            ->orderBy('break_down_resource_shadows.id');
    }

    function getPreviousUnitPriceAttribute()
    {
        if ($this->previous_qty) {
            return $this->previous_cost / $this->previous_qty;
        }

        return 0;
    }

    function getToDateUnitPriceAttribute()
    {
        if ($this->to_date_qty) {
            return $this->to_date_cost / $this->to_date_qty;
        }

        return 0;
    }

    function getAllowableEvCostAttribute()
    {
        if (!$this->budget_cost) {
            return 0;
        }

        if ($this->progress_value == 1 || $this->to_date_cost > $this->budget_cost || $this->to_date_qty > $this->budget_qty) {
            return $this->budget_cost;
        }

        return $this->to_date_qty * $this->unit_price;
    }

    function getAllowableVarAttribute()
    {
        return $this->allowable_ev_cost - $this->to_date_cost;
    }

    function getBlAllowableCostAttribute()
    {
        return $this->budget_cost - $this->allowable_ev_cost;
    }

    function getBlAllowableVarAttribute()
    {
        return $this->bl_allowable_cost - $this->remaining_cost;
    }

    function getRemainingQtyAttribute()
    {
        if (!$this->budget_unit || $this->progress_value == 1) {
            return 0;
        }

        if ($this->to_date_qty > $this->budget_unit && $this->progress_value) {
            return ($this->to_date_qty * (1 - $this->progress_value)) / $this->progress_value;
        }

        return $this->budget_unit - $this->to_date_qty;
    }

    function getRemainingCostAttribute()
    {
        return $this->remaining_unit_price * $this->remaining_qty;
    }

    function getRemainingUnitPriceAttribute()
    {
        $resource = CostResource::where('resource_id', $this->resource_id)
            ->where('project_id', $this->project_id)->where('period_id', $this->period_id)->first();

        if ($resource) {
            return $resource->rate;
        }

        return $this->to_date_price;
    }

    function getCompletionCostAttribute()
    {
        return $this->remaining_cost + $this->to_date_cost;
    }

    function getCompletionQtyAttribute()
    {
        return $this->remaining_qty + $this->to_date_qty;

    }

    function getCompletionUnitPriceAttribute()
    {
        if ($this->completion_qty == 0) {
            return $this->remaining_unit_price;
        } else {
            return $this->completion_cost / $this->completion_qty;
        }
    }

    function getUnitPriceVarAttribute()
    {
        return $this->unit_price - $this->completion_unit_price;
    }

    function getQtyVarAttribute()
    {
        return $this->budget_qty - $this->completion_qty;
    }

    function getCostVarAttribute()
    {
        return $this->budget_cost - $this->completion_cost;
    }

    function getPhysicalUnitAttribute()
    {
        if ($this->budget_unit == 0) {
            return 0;
        }

        return $this->to_date_cost / $this->budget_unit;
    }

    function getPwIndexAttribute()
    {
        if ($this->budget_unit == 0 || $this->progress_value < 1) {
            return 0;
        }

        return ($this->budget_unit - $this->to_date_qty) / $this->budget_unit;

    }

    function getAllowableQtyAttribute()
    {
        if (($this->to_date_qty < $this->budget_unit) && $this->progress_value != 1) {
            return $this->to_date_qty;
        }

        return $this->budget_unit;
    }

    function getCostVarianceToDateDueUnitPriceAttribute()
    {
        return ($this->unit_price - $this->to_date_unit_price) * $this->to_date_qty;
    }

    function getCostVarianceRemainingDueUnitPriceAttribute()
    {
        return ($this->unit_price - $this->remaining_unit_price) * $this->remaining_qty;
    }

    function getCostVarianceCompletionDueUnitPriceAttribute()
    {
        return $this->cost_variance_to_date_due_unit_price + $this->cost_variance_remaining_due_unit_price;
    }

    function getCostVarianceCompletionDueQtyAttribute()
    {
        return $this->unit_price * ($this->budget_unit - $this->completion_qty);
    }

    function getCostVarianceToDateDueQtyAttribute()
    {
        return $this->unit_price * ($this->allowable_qty - $this->to_date_qty);
    }

    function getProgressValueAttribute()
    {
        return $this->progress / 100;
    }
}
