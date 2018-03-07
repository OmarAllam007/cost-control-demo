<?php

namespace App;

use App\Behavior\RollupResource;
use App\Behaviors\CostAttributes;
use App\Behaviors\HasChangeLog;
use App\Behaviors\Tree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;


/**
 * @property Collection actual_resources
 */
class BreakDownResourceShadow extends Model
{
    use Tree, HasChangeLog;
    use CostAttributes;
//    use CostAttributes {
//        getAllowableEvCostAttribute as protected getAllowableEvCostAttributeFromTrait;
//        getRemainingCostAttribute as protected getRemainingCostAttributeFromTrait;
//        getRemainingQtyAttribute as protected getRemainingQtyAttributeFromTrait;
//        getRemainingUnitPriceAttribute as protected getRemainingUnitPriceAttributeFromTrait;
//    }

    protected $table = 'break_down_resource_shadows';
    protected $fillable = [
        'breakdown_resource_id', 'code', 'project_id', 'wbs_id', 'breakdown_id', 'resource_id', 'template', 'activity', 'activity_id', 'cost_account',
        'eng_qty', 'budget_qty', 'resource_qty', 'resource_waste', 'resource_type', 'resource_type_id', 'resource_code', 'resource_name',
        'unit_price', 'measure_unit', 'budget_unit', 'budget_cost', 'boq_equivilant_rate', 'labors_count', 'productivity_output', 'productivity_ref', 'remarks', 'productivity_id', 'template_id', 'unit_id',
        'progress', 'status', 'boq_id', 'boq_wbs_id'
    ];

    protected $casts = ['boolean' => ['show_in_cost', 'show_in_budget']];

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

    function qty_survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
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
        $relation = $this->hasOne(CostShadow::class, 'breakdown_resource_id', 'breakdown_resource_id');
        $relation->where('period_id', '<=', $this->getCalculationPeriod()->id)->orderBy('period_id', 'DESC');
        return $relation;
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

    function scopeJoinCost(Builder $query, WbsLevel $wbsLevel = null, Period $period)
    {
        $query->from("$this->table as budget")
            ->leftJoin('cost_shadows as cost', function(JoinClause $on) use ($period) {
                $on->where('budget.breakdown_resource_id', '=', 'budget.breakdown_resource_id')
                    ->where('cost.period_id', '=', $period->id);
            })
//            ->leftJoin('current_resources as curr', 'budget.breakdown_resource_id', '=', 'curr.curr_breakdown_resource_id')
//            ->leftJoin('previous_resources as prev', 'budget.breakdown_resource_id', '=', 'prev.prev_breakdown_resource_id')
            ->selectRaw('budget.*, cost.curr_qty, cost.curr_cost, cost.curr_unit_price, cost.prev_qty, cost.prev_cost, prev_unit_price');

        if ($wbsLevel) {
            $query->whereIn('budget.wbs_id', $wbsLevel->getChildrenIds());
        }

        $this->appendFields();

        return $query;
    }

    function boq()
    {
        return $this->belongsTo(Boq::class,'boq_id');
    }

    function getSurveyAttribute()
    {
        if ($this->qty_survey) {
            return $this->qty_survey;
        }

        $qty_survey = Survey::whereIn('wbs_level_id', $this->wbs->getParentIds())
            ->where('cost_account', $this->cost_account)->orderBy('wbs_level_id', 'desc')->first();

        return $qty_survey;
    }

    function getDescriptorAttribute()
    {
        return ($this->wbs->path ?? '') . ' / ' . $this->activity . ' / ' . $this->resource_name;
    }

    use RollupResource;
}
