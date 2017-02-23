<?php

namespace App;

use App\Behaviors\CostAttributes;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;


class BreakDownResourceShadow extends Model
{
    use Tree, CostAttributes;

    protected $table = 'break_down_resource_shadows';
    protected $fillable = [
        'breakdown_resource_id', 'code', 'project_id', 'wbs_id', 'breakdown_id', 'resource_id', 'template', 'activity', 'activity_id', 'cost_account',
        'eng_qty', 'budget_qty', 'resource_qty', 'resource_waste', 'resource_type', 'resource_type_id', 'resource_code', 'resource_name',
        'unit_price', 'measure_unit', 'budget_unit', 'budget_cost', 'boq_equivilant_rate', 'labors_count', 'productivity_output', 'productivity_ref', 'remarks', 'productivity_id', 'template_id', 'unit_id',
        'progress', 'status'
    ];

    public $update_cost = false;

    function scopeByRawData(Builder $query, $row)
    {
        return $query->where('code', trim($row[0]))
            ->where('resource_code', trim($row[7]))
            ->first();
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class);
    }

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function std_activity()
    {
        return $this->belongsTo(StdActivity::class, 'activity_id');
    }

    function breakdown_resource()
    {
        return $this->belongsTo(BreakdownResource::class);
    }

    function resource()
    {
        return $this->belongsTo(Resources::class);
    }

    function breakdown()
    {
        return $this->belongsTo(Breakdown::class);
    }


    function cost()
    {
        return $this->hasOne(CostShadow::class, 'breakdown_resource_id', 'breakdown_resource_id')->where('period_id', $this->project->open_period()->id);
    }

    function wbs_resource()
    {
        return $this->belongsTo(WbsResource::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }

    function scopeSumFields(Builder $q, $group, $fields = [])
    {
        foreach ($fields as $field) {
            $q->groupBy("$group")->select($group)->selectRaw("SUM($field) as $field")->get();
        }
    }

    function productivity()
    {
        return $this->belongsTo(Productivity::class);
    }

    public function recalculate()
    {

    }

    function scopeJoinCost(Builder $query, WbsLevel $wbsLevel = null, Period $period = null)
    {
        $query->from("$this->table as budget")
            ->leftJoin('cost_shadows as cost', function(JoinClause $join) use ($period) {
                $join->on('budget.breakdown_resource_id', '=', 'cost.breakdown_resource_id');

                if ($period) {
                    $join->where('cost.period_id', '=', $period->id);
                }

                return $join;
            })
            ->selectRaw('budget.*, cost.id as cost_id, cost.curr_cost, cost.curr_qty, cost.curr_unit_price, cost.prev_cost, cost.prev_qty, ' .
                'cost.to_date_cost, cost.to_date_qty, cost.prev_unit_price, cost.to_date_unit_price, ' .
                'cost.allowable_ev_cost, cost.allowable_var, cost.bl_allowable_cost, cost.bl_allowable_var, ' .
                'cost.remaining_qty, cost.remaining_cost, cost.remaining_unit_price, ' .
                'cost.completion_qty, cost.completion_cost, cost.completion_unit_price, ' .
                'cost.qty_var, cost.cost_var, cost.unit_price_var, cost.physical_unit, cost.pw_index, ' .
                'cost.cost_variance_to_date_due_unit_price, cost.allowable_qty, cost.cost_variance_remaining_due_unit_price, ' .
                'cost.cost_variance_completion_due_unit_price, cost.cost_variance_completion_due_qty, cost.cost_variance_to_date_due_qty');

        if ($wbsLevel) {
            $query->whereIn('budget.wbs_id', $wbsLevel->getChildrenIds());
        }

        $this->appendFields();

        return $query;
    }

    /*    function getBoqDescription()
        {

        }*/
}
