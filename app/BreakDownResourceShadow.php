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

    function previous()
    {
        return $this->hasOne(PreviousCost::class, 'prev_breakdown_resource_id', 'breakdown_resource_id');
    }

    function current()
    {
        return $this->hasOne(CurrentCost::class, 'curr_breakdown_resource_id', 'breakdown_resource_id');
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

    function scopeJoinCost(Builder $query, WbsLevel $wbsLevel = null)
    {
        $query->from("$this->table as budget")
            ->leftJoin('current_resources as curr', 'budget.breakdown_resource_id', '=', 'curr.curr_breakdown_resource_id')
            ->leftJoin('previous_resources as prev', 'budget.breakdown_resource_id', '=', 'prev.prev_breakdown_resource_id')
            ->selectRaw('budget.*, curr.curr_qty, curr.curr_cost, curr_unit_price, prev.prev_qty, prev.prev_cost, prev_unit_price');

        if ($wbsLevel) {
            $query->whereIn('budget.wbs_id', $wbsLevel->getChildrenIds());
        }

        $this->appendFields();

        return $query;
    }

    function getQtyToDateAttribute()
    {
        return ActualResources::where('breakdown_resource_id', $this->breakdown_resource_id)->sum('qty');
    }

    public function getCurrQtyAttribute()
    {
        if (isset($this->cost->curr_qty)) {
            return $this->cost->curr_qty;
        }

        if (isset($this->current->curr_qty)) {
            return $this->current->curr_qty;
        }

        return 0;
    }

    public function getCurrCostAttribute()
    {
        if (isset($this->cost->curr_cost)) {
            return $this->cost->curr_cost;
        }

        if (isset($this->current->curr_cost)) {
            return $this->current->curr_cost;
        }

        return 0;
    }

    public function getCurrUnitPriceAttribute()
    {
        if (isset($this->cost->curr_unit_price)) {
            return $this->cost->curr_unit_price;
        }

        if (isset($this->current->curr_unit_price)) {
            return $this->current->curr_unit_price;
        }

        return 0;
    }

    public function getPrevQtyAttribute()
    {
        if (isset($this->cost->prev_qty)) {
            return $this->cost->prev_qty;
        }

        if (isset($this->previous->prev_qty)) {
            return $this->previous->prev_qty;
        }

        return 0;
    }

    public function getPrevCostAttribute()
    {
        if (isset($this->cost->prev_cost)) {
            return $this->cost->prev_cost;
        }

        if (isset($this->previous->prev_cost)) {
            return $this->previous->prev_cost;
        }

        return 0;
    }

    public function getPrevUnitPriceAttribute()
    {
        if (isset($this->cost->prev_unit_price)) {
            return $this->cost->prev_unit_price;
        }

        if (isset($this->previous->prev_unit_price)) {
            return $this->previous->prev_unit_price;
        }

        return 0;
    }
    /*    function getBoqDescription()
        {

        }*/
}
