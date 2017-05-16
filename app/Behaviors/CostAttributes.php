<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 2/20/17
 * Time: 2:34 PM
 */

namespace App\Behaviors;


use App\ActualResources;
use App\BreakDownResourceShadow;
use App\CostResource;
use App\Period;
use App\StdActivity;

trait CostAttributes
{
    protected $calculated;

    function getPreviousUnitPriceAttribute()
    {
        if ($this->previous_qty) {
            return $this->previous_cost / $this->previous_qty;
        }

        return 0;
    }

    function getToDateQtyAttribute()
    {
        if (!empty($this->attributes['to_date_qty'])) {
            return $this->attributes['to_date_qty'];
        }

        return $this->curr_qty + $this->prev_qty;
    }

    function getToDateCostAttribute()
    {
        if (!empty($this->attributes['to_date_cost'])) {
            return $this->attributes['to_date_cost'];
        }

        return $this->curr_cost + $this->prev_cost;
    }

    function getToDateUnitPriceAttribute()
    {
        if (!empty($this->attributes['to_date_unit_price'])) {
            return $this->attributes['to_date_unit_price'];
        }

        if (isset($this->calculated['to_date_unit_price'])) {
            return $this->calculated['to_date_unit_price'];
        }

        if ($this->to_date_qty) {
            return $this->calculated['to_date_unit_price'] = $this->to_date_cost / $this->to_date_qty;
        }

        return 0;
    }

    function getAllowableEvCostAttribute()
    {
        if (isset($this->calculated['allowable_ev_cost'])) {
            return $this->calculated['allowable_ev_cost'];
        }

        if (!$this->budget_cost) {
            return 0;
        }

        $activity = StdActivity::find($this->activity_id);
        if ($activity->isGeneral()) {
            return $this->calculated['allowable_ev_cost'] = $this->progress_value * $this->budget_cost;
        }

        if ($this->progress_value == 1 || $this->to_date_cost > $this->budget_cost || $this->to_date_qty > $this->budget_unit) {
            return $this->calculated['allowable_ev_cost'] = $this->budget_cost;
        }

        return $this->calculated['allowable_ev_cost'] = $this->to_date_qty * $this->unit_price;
    }

    function getAllowableVarAttribute()
    {
        if (isset($this->calculated['allowable_var'])) {
            return $this->calculated['allowable_var'];
        }

        return $this->calculated['allowable_var'] = $this->latest_allowable_cost - $this->to_date_cost;
    }

    function getBlAllowableCostAttribute()
    {
        if (isset($this->calculated['bl_allowable_cost'])) {
            return $this->calculated['bl_allowable_cost'];
        }

        return $this->calculated['bl_allowable_cost'] = $this->budget_cost - $this->latest_allowable_cost;
    }

    function getBlAllowableVarAttribute()
    {
        if (isset($this->calculated['bl_allowable_var'])) {
            return $this->calculated['bl_allowable_var'];
        }

        return $this->calculated['bl_allowable_var'] = $this->bl_allowable_cost - $this->remaining_cost;
    }

    function getRemainingQtyAttribute()
    {
        if (isset($this->calculated['remaining_qty'])) {
            return $this->calculated['remaining_qty'];
        }

        if (!$this->budget_unit || $this->progress_value == 1 || strtolower($this->status) == 'closed') {
            return $this->calculated['remaining_qty'] = 0;
        }

        if ($this->to_date_qty > $this->budget_unit && $this->progress_value) {
            return $this->calculated['remaining_qty'] = ($this->to_date_qty * (1 - $this->progress_value)) / $this->progress_value;
        }

        $remaining = $this->budget_unit - $this->to_date_qty;
        if ($remaining < 0) {
            $remaining = 0;
        }

        return $this->calculated['remaining_qty'] = $remaining;
    }

    function getRemainingCostAttribute()
    {
        if (isset($this->calculated['remaining_cost'])) {
            return $this->calculated['remaining_cost'];
        }

        return $this->calculated['remaining_cost'] = $this->remaining_unit_price * $this->remaining_qty;
    }

    function getRemainingUnitPriceAttribute()
    {

        if (isset($this->calculated['remaining_unit_price'])) {
            return $this->calculated['remaining_unit_price'];
        }

        if ($this->curr_unit_price) {
            return $this->curr_unit_price;
        }

        $resource = CostResource::where('resource_id', $this->resource_id)
            ->where('project_id', $this->project_id)->where('period_id', $this->getCalculationPeriod()->id)->first();

        if ($resource) {
            return $this->calculated['remaining_unit_price'] = $resource->rate;
        }

        if ($this->prev_unit_price) {
            return $this->calculated['remaining_unit_price'] = $this->prev_unit_price;
        }

        return $this->calculated['remaining_unit_price'] = $this->unit_price;
    }

    function getCompletionCostAttribute()
    {
        if (isset($this->calculated['completion_cost'])) {
            return $this->calculated['completion_cost'];
        }

        return $this->calculated['completion_cost'] = $this->latest_remaining_cost + $this->to_date_cost;
    }

    function getCompletionQtyAttribute()
    {
        if (isset($this->calculated['completion_qty'])) {
            return $this->calculated['completion_qty'];
        }

        return $this->calculated['completion_qty'] = $this->latest_remaining_qty + $this->to_date_qty;
    }

    function getCompletionUnitPriceAttribute()
    {
        if (isset($this->calculated['completion_unit_price'])) {
            return $this->calculated['completion_unit_price'];
        }

        if ($this->completion_qty == 0) {
            return $this->calculated['completion_unit_price'] = $this->latest_remaining_unit_price;
        } else {
            return $this->calculated['completion_unit_price'] = $this->completion_cost / $this->completion_qty;
        }
    }

    function getUnitPriceVarAttribute()
    {
        if (isset($this->calculated['unit_price_var'])) {
            return $this->calculated['unit_price_var'];
        }
        return $this->calculated['unit_price_var'] = $this->unit_price - $this->completion_unit_price;
    }

    function getQtyVarAttribute()
    {
        if (isset($this->calculated['qty_var'])) {
            return $this->calculated['qty_var'];
        }
        return $this->calculated['qty_var'] = $this->budget_unit - $this->completion_qty;
    }

    function getCostVarAttribute()
    {
        if (isset($this->calculated['cost_var'])) {
            return $this->calculated['cost_var'];
        }
        return $this->calculated['cost_var'] = $this->budget_cost - $this->completion_cost;
    }

    function getPhysicalUnitAttribute()
    {
        if ($this->budget_unit_rate == 0) {
            return 0;
        }

        return $this->to_date_cost / $this->budget_unit_rate;
    }

    function getPwIndexAttribute()
    {
        $resource_id = $this->resource_id;
        $period_id = $this->period_id;

        $query = '
SELECT sum(allowable_qty) allowable, sum(to_date_qty) qty 
FROM cost_shadows cost 
LEFT JOIN break_down_resource_shadows budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id) 
WHERE cost.resource_id = :resource_id AND (budget.progress = 100 OR cost.to_date_qty >= budget.budget_cost) 
AND period_id = (SELECT max(period_id) FROM cost_shadows p WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND p.period_id <= :period_id)
';

        $result = \DB::selectOne($query, compact('resource_id', 'period_id'));
        if ($result && $result->allowable) {
            return ($result->allowable - $result->qty) * 100 / $result->allowable;
        }

        return 0;
    }

    function getAllowableQtyAttribute()
    {
        if (isset($this->calculated['allowable_qty'])) {
            return $this->calculated['allowable_qty'];
        }

        if (($this->to_date_qty < $this->budget_unit)) {
            return $this->calculated['allowable_qty'] = $this->to_date_qty;
        }

        return $this->calculated['allowable_qty'] = $this->budget_unit;
    }

    function getCostVarianceToDateDueUnitPriceAttribute()
    {
        if (isset($this->calculated['cost_variance_to_date_due_unit_price'])) {
            return $this->calculated['cost_variance_to_date_due_unit_price'];
        }

        return $this->calculated['cost_variance_to_date_due_unit_price'] = ($this->unit_price - $this->to_date_unit_price) * $this->to_date_qty;
    }

    function getCostVarianceRemainingDueUnitPriceAttribute()
    {
        if (isset($this->calculated['cost_variance_remaining_due_unit_price'])) {
            return $this->calculated['cost_variance_remaining_due_unit_price'];
        }

        return $this->calculated['cost_variance_remaining_due_unit_price'] = ($this->unit_price - $this->remaining_unit_price) * $this->remaining_qty;
    }

    function getCostVarianceCompletionDueUnitPriceAttribute()
    {
        if (isset($this->calculated['cost_variance_completion_due_unit_price'])) {
            return $this->calculated['cost_variance_completion_due_unit_price'];
        }

        return $this->calculated['cost_variance_completion_due_unit_price'] = $this->cost_variance_to_date_due_unit_price + $this->cost_variance_remaining_due_unit_price;
    }

    function getCostVarianceCompletionDueQtyAttribute()
    {
        if (isset($this->calculated['cost_variance_completion_due_qty'])) {
            return $this->calculated['cost_variance_completion_due_qty'];
        }

        return $this->calculated['cost_variance_completion_due_qty'] = $this->unit_price * ($this->budget_unit - $this->completion_qty);
    }

    function getCostVarianceToDateDueQtyAttribute()
    {
        if (isset($this->calculated['cost_variance_to_date_due_qty'])) {
            return $this->calculated['cost_variance_to_date_due_qty'];
        }

        return $this->calculated['cost_variance_to_date_due_qty'] = $this->unit_price * ($this->allowable_qty - $this->to_date_qty);
    }

    function getBudgetUnitRateAttribute()
    {
        // Sum BoqUnitRate on all resource for the cost account on WBS
        if (isset($this->calculated['budget_unit_rate'])) {
            return $this->calculated['budget_unit_rate'];
        }

        return $this->calculated['budget_unit_rate'] = BreakDownResourceShadow::where([
            'cost_account' => $this->cost_account, 'wbs_id' => $this->wbs_id
        ])->sum('boq_equivilant_rate');
    }

    function getProgressValueAttribute()
    {
        if (strtolower($this->status) == 'closed') {
            return 1;
        }

        return $this->progress / 100;
    }

    function appendFields()
    {
        return $this->appends = [
            'to_date_qty', 'to_date_cost', 'to_date_unit_price', 'allowable_ev_cost', 'allowable_var', 'bl_allowable_cost', 'bl_allowable_var', 'remaining_qty', 'remaining_cost',
            'remaining_unit_price', 'completion_cost', 'completion_qty', 'completion_unit_price', 'unit_price_var', 'qty_var', 'cost_var', 'physical_unit', 'allowable_qty',
            'cost_variance_to_date_due_unit_price', 'cost_variance_remaining_due_unit_price', 'cost_variance_completion_due_unit_price', 'cost_variance_completion_due_qty',
            'cost_variance_to_date_due_qty',
            //'budget_unit_rate',
            //'pw_index',
        ];
    }

    /** @var Period */
    protected $calculation_period;

    function setCalculationPeriod($period = null)
    {
        if (!$period) {
            $period = $this->project->open_period();
        } elseif (is_int($period)) {
            $period = Period::find($period);
        }

        $this->calculation_period = $period;
        return $this;
    }

    function getCalculationPeriod()
    {
        if (!$this->calculation_period) {
            $this->calculation_period = $this->project->open_period();
        }

        return $this->calculation_period;
    }

    public function getLatestAllowableCostAttribute()
    {
        return $this->allowable_ev_cost;
    }

    function getLatestRemainingCostAttribute()
    {
        return $this->remaining_cost;
    }

    function getLatestRemainingQtyAttribute()
    {
        return $this->remaining_qty;
    }

    function getLatestRemainingUnitPriceAttribute()
    {
        return $this->remaining_unit_price;
    }

    function actual_resources()
    {
        return $this->hasMany(ActualResources::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }
}