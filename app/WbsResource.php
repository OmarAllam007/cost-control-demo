<?php

namespace App;

use App\Behaviors\CostAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WbsResource extends Model
{
    protected $appends = [
        'to_date_unit_price',
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

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_id');
    }

    function shadow()
    {
        return $this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }

    function period()
    {
        return $this->belongsTo(Period::class);
    }

    function scopeJoinShadow(Builder $query)
    {
        return $query->join('break_down_resource_shadows', 'break_down_resource_shadows.breakdown_resource_id', '=', 'wbs_resources.breakdown_resource_id')
            ->select('break_down_resource_shadows.*', 'wbs_resources.*')
            ->orderBy('break_down_resource_shadows.id');
    }

    use CostAttributes;
}
