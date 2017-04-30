<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Support\CostShadowCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CostShadow extends Model
{
    use HasChangeLog;

    protected $fillable = [
        "project_id", "wbs_level_id", "period_id", "resource_id", "breakdown_resource_id", "curr_cost", "curr_qty",
        "curr_unit_price", "prev_cost", "prev_qty", "to_date_cost", "to_date_qty", "prev_unit_price",
        "to_date_unit_price", "progress", "allowable_ev_cost", "allowable_var", "bl_allowable_cost", "bl_allowable_var",
        "remaining_qty", "remaining_cost", "remaining_unit_price", "completion_qty", "completion_cost", "completion_unit_price",
        "qty_var", "cost_var", "unit_price_var", "physical_unit", "pw_index", "cost_variance_to_date_due_unit_price",
        "allowable_qty", "cost_variance_remaining_due_unit_price", "cost_variance_completion_due_unit_price",
        "cost_variance_completion_due_qty", "cost_variance_to_date_due_qty", 'batch_id', 'doc_no', 'budget_unit_rate'
    ];

    function budget()
    {
        return $this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class);
    }

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function period()
    {
        return $this->belongsTo(Period::class);
    }

    function scopeJoinBudget(Builder $query, $group)
    {
        $query->from('cost_shadows as cost')
            ->join('break_down_resource_shadows as budget', 'budget.breakdown_resource_id', '=', 'cost.breakdown_resource_id')
            ->groupBy($group)->select($group);
    }

    function scopeSumFields(Builder $query, $fields = [])
    {
        foreach ($fields as $field) {
            $query->selectRaw("SUM($field) as " . explode('.', $field)[1]);
        }
    }

    function scopeSumColumns(Builder $query, $fields = [])
    {
        foreach ($fields as $field) {
            $query->selectRaw("SUM($field) as " . $field);
        }
    }

    function scopeJoinShadow(Builder $query, WbsLevel $level = null, Period $period = null, $type = 'left')
    {
        $query->selectRaw('csh.*, bsh.*, csh.id as cost_id')
            ->from('cost_shadows as csh')
            ->join('break_down_resource_shadows as bsh', 'csh.breakdown_resource_id', '=', 'bsh.breakdown_resource_id');

        if ($level) {
            $query->whereIn('wbs_level_id', $level->getChildrenIds());
        }

        if ($period) {
            $query->where('period_id', $period->id);
        }

        return $query;
    }

    function recalculate($keepRemaining)
    {
        return (new CostShadowCalculator($this, $keepRemaining))->update();
    }

    function getProgressAttribute()
    {
        return $this->budget->progress;
    }

    function getStatusAttribute()
    {
        return $this->budget->status;
    }

    function getBudgetUnitAttribute()
    {
        return $this->budget->budget_unit;
    }

    function getBudgetCostAttribute()
    {
        return $this->budget->budget_cost;
    }
}
