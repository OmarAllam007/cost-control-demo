<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CostShadow extends Model
{
    protected $fillable = ["project_id", "wbs_level_id", "period_id", "resource_id", "breakdown_resource_id", "current_cost", "current_qty", "current_unit_price", "previous_cost", "previous_qty", "to_date_cost", "to_date_qty", "previous_unit_price", "to_date_unit_price", "progress", "allowable_ev_cost", "allowable_var", "bl_allowable_cost", "bl_allowable_var", "remaining_qty", "remaining_cost", "remaining_unit_price", "completion_qty", "completion_cost", "completion_unit_price", "qty_var", "cost_var", "unit_price_var", "physical_unit", "pw_index", "cost_variance_to_date_due_unit_price", "allowable_qty", "cost_variance_remaining_due_unit_price", "cost_variance_completion_due_unit_price", "cost_variance_completion_due_qty", "cost_variance_to_date_due_qty"];

    function budget()
    {
        return $this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }




}
