<?php

namespace App\Behaviors;

use App\ActualResources;
use App\BreakDownResourceShadow;
use App\CostResource;
use App\CostShadow;
use App\Period;
use App\Resources;
use App\ResourceType;
use App\StdActivity;

/**
 * @property float $budget_cost
 * @property float $to_date_cost
 * @property float $to_date_qty
 * @property float $to_date_unit_price
 * @property float $allowable_ev_cost
 * @property float $allowable_qty
 * @property float $completion_cost
 * @property float $completion_qty
 * @property float $remaining_cost
 * @property float $remaining_qty
 * @property float $remaining_unit_price
 * @property float $previous_qty
 * @property float $previous_cost
 * @property float $progress
 * @property float $progress_value
 * @property float $unit_price
 * @property float $budget_unit
 * @property float $calculated
 * @property float $cpi
 * @property float $cost_var
 * @property float $completion_cost_optimistic
 * @property float $completion_cost_likely
 * @property float $completion_cost_pessimistic
 * @property float $completion_var_optimistic
 * @property float $completion_var_likely
 * @property float $completion_var_pessimistic
 */
trait CostAttributes
{
    public $ignore_cost = false;
    public $update_cost = false;
    protected $calculated;
    protected $completion_values;

    /** @var CostShadow */
    protected $latestCost = null;

    protected function getLatestCost()
    {
        if ($this->latestCost) {
            return $this->latestCost;
        }

        return $this->latestCost = CostShadow::where('period_id', '<=', $this->getCalculationPeriod()->id)
            ->where('breakdown_resource_id', $this->breakdown_resource_id)
            ->orderBy('period_id', 'desc')->first();
    }

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

        return ActualResources::where('breakdown_resource_id', $this->breakdown_resource_id)->sum('qty') ?: 0;
    }

    function getToDateCostAttribute()
    {
        if (!empty($this->attributes['to_date_cost'])) {
            return $this->attributes['to_date_cost'];
        }

        return ActualResources::where('breakdown_resource_id', $this->breakdown_resource_id)->sum('cost') ?: 0;
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

        $latest = $this->getLatestCost();
        if ($latest && $latest->manual_edit) {
            return $this->calculated['allowable_ev_cost'] = $latest->allowable_ev_cost;
        }

        if (!$this->budget_cost) {
            return 0;
        }

        if ($this->isGeneral()) {
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

        if (!$this->remaining_qty == 0 || $this->progress_value = 1 || strtolower($this->status) == 'closed') {
            return $this->calculated['bl_allowable_cost'] = 0;
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

        $latest = $this->getLatestCost();
        if ($latest && $latest->manual_edit) {
            return $this->calculated['remaining_qty'] = $latest->remaining_qty;
        }


        if ($this->to_date_qty > $this->budget_unit && $this->progress_value) {
            return $this->calculated['remaining_qty'] = ($this->to_date_qty * (1 - $this->progress_value)) / $this->progress_value;
        }

        $remaining = $this->budget_unit - $this->to_date_qty;
        return $this->calculated['remaining_qty'] = $remaining;
    }

    function getRemainingCostAttribute()
    {
        if (isset($this->calculated['remaining_cost'])) {
            return $this->calculated['remaining_cost'];
        }

        if ($this->isGeneral()) {
            return ($this->budget_cost - $this->allowable_ev_cost) / $this->cpi;
        }

        if ($this->is_rollup && !$this->isCostAccountRollup()) {
            if (!$this->to_date_cost) {
                return $this->budget_cost;
            }

            $cpi = $this->allowable_ev_cost / $this->to_date_cost;
            return $this->calculated['remaining_cost'] =  max(0, ($this->budget_cost - $this->allowable_ev_cost) / $cpi); //$this->completion_cost - $this->to_date_cost;
        }

        return $this->calculated['remaining_cost'] = $this->remaining_unit_price * $this->remaining_qty;
    }

    function getRemainingUnitPriceAttribute()
    {
        if (isset($this->calculated['remaining_unit_price'])) {
            return $this->calculated['remaining_unit_price'];
        }

        $conditions = ['project_id' => $this->project_id];

        $resource = Resources::find($this->resource_id);

        if (!$resource->rate) {
            return $this->calculated['remaining_unit_price'] = 0;
        }

        if ($resource->isMaterial()) {
            // For material we calculate over resource in all activities
            $conditions['resource_id'] = $this->resource_id;
        } else {
            // For non-material we calculate on the resource in each activity
            $conditions['breakdown_resource_id'] = $this->breakdown_resource_id;
        }

        $latest = CostShadow::where($conditions)->orderBy('period_id', 'desc')->first();
        if ($latest && $latest->manual_edit) {
            return $this->calculated['remaining_unit_price'] = $latest->remaining_unit_price;
        }

        // Find current data for the resource
        $current = ActualResources::where($conditions)
            ->where('period_id', $this->getCalculationPeriod()->id)
            ->selectRaw('sum(cost)/sum(qty) as unit_price')->value('unit_price');

        if ($current !== null) {
            $remainingUnitPrice = $current;
        } else {
            // If no current data, find to date data for the resource
            $todate = ActualResources::where($conditions)
                ->where('period_id', '<=', $this->getCalculationPeriod()->id)
                ->selectRaw('sum(cost)/sum(qty) as unit_price')->value('unit_price');


            if ($todate !== null) {
                $remainingUnitPrice = $todate;
            } else {
                // If the resource didn't start use budget unit rate
//                $budgetResource = Resources::find($this->resource_id);
                $remainingUnitPrice = $this->unit_price;
            }
        }
        
        return $this->calculated['remaining_unit_price'] = $remainingUnitPrice;
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

        if ($this->progress == 100 || strtolower($this->status) == 'closed') {
            return $this->calculated['allowable_qty'] = $this->budget_unit;
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

    function getWbsLevelIdAttribute()
    {
        if (!empty($this->attributes['wbs_level_id'])) {
            return $this->attributes['wbs_level_id'];
        }
        return $this->wbs_id;
    }

    function appendFields()
    {
        return $this->appends = [
            'to_date_qty', 'to_date_cost', 'to_date_unit_price', 'allowable_ev_cost', 'allowable_var', 'bl_allowable_cost', 'bl_allowable_var', 'remaining_qty', 'remaining_cost',
            'remaining_unit_price', 'completion_cost', 'completion_qty', 'completion_unit_price', 'unit_price_var', 'qty_var', 'cost_var', 'physical_unit', 'allowable_qty',
            'cost_variance_to_date_due_unit_price', 'cost_variance_remaining_due_unit_price', 'cost_variance_completion_due_unit_price', 'cost_variance_completion_due_qty',
            'cost_variance_to_date_due_qty', 'latest_remaining_qty', 'latest_remaining_cost', 'latest_remaining_unit_price', 'curr_cost', 'curr_qty',
            'curr_unit_price', 'prev_cost', 'prev_qty', 'prev_unit_price', 'wbs_level_id', 'to_date_price_var', 'to_date_qty_var',
            'completion_cost_optimistic', 'completion_cost_likely', 'completion_cost_pessimistic',
            'completion_var_optimistic', 'completion_var_likely', 'completion_var_pessimistic'
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

    function getToDatePriceVarAttribute()
    {
        return $this->unit_price - $this->to_date_unit_price;
    }

    function getToDateQtyVarAttribute()
    {
        return $this->allowable_qty - $this->to_date_qty;
    }

    function actual_resources()
    {
        return $this->hasMany(ActualResources::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }

    function isGeneral()
    {
        if (isset($this->is_general_requirement)) {
            return $this->is_general_requirement;
        }

        $activity = StdActivity::find($this->activity_id);
        return $this->is_general_requirement = $activity->isGeneral();
    }

    function getCompletionCostOptimisticAttribute()
    {
        if (!$this->isGeneral()) {
            return $this->completion_cost;
        }

        // ETC = BC - EV
        return ($this->budget_cost - $this->allowable_ev_cost) + $this->to_date_cost;
    }

    function getCompletionCostLikelyAttribute()
    {
        // ETC = (BC - EV) / cpi
        // Revise Remaining Cost
        return $this->completion_cost;
    }

    function getCompletionCostPessimisticAttribute()
    {
        // ETC = (BC - EV) / (cpi * spi)

        if (!$this->isGeneral()) {
            return $this->completion_cost;
        }

        $spi = $this->getCalculationPeriod()->spi_index ?: 1;

        return (($this->budget_cost - $this->allowable_ev_cost) / ($spi * $this->cpi)) + $this->to_date_cost;
    }

    function getCompletionVarOptimisticAttribute()
    {
        if (!$this->isGeneral()) {
            return $this->cost_var;
        }

        return $this->budget_cost - $this->completion_cost_optimistic;
    }

    function getCompletionVarLikelyAttribute()
    {
        if (!$this->isGeneral()) {
            return $this->cost_var;
        }

        return $this->budget_cost - $this->completion_cost_likely;
    }

    function getCompletionVarPessimisticAttribute()
    {
        if (!$this->isGeneral()) {
            return $this->cost_var;
        }

        return $this->budget_cost - $this->completion_cost_pessimistic;
    }

    private function completionValues()
    {
        if (isset($this->completion_values)) {
            return $this->completion_values;
        }

        $spi = $this->getCalculationPeriod()->spi_index ?: 1;

        $this->completion_values = [
            $this->completion_cost,
            ($this->budget_cost - $this->allowable_ev_cost) + $this->to_date_cost,
            (($this->budget_cost - $this->allowable_ev_cost) / ($spi * $this->cpi)) + $this->to_date_cost
        ];


        sort($this->completion_values);
        return $this->completion_values;
    }

    public function getCpiAttribute()
    {
        $cpi = 1;
        if ($this->to_date_cost && $this->allowable_ev_cost) {
            $cpi = $this->allowable_ev_cost / $this->to_date_cost;
        }
        return $cpi;
    }


    function getQtyToDateAttribute()
    {
        return $this->actual_resources()->sum('qty');
    }

    public function getCurrQtyAttribute()
    {
        $period_id = $this->getCalculationPeriod()->id;
        if (!empty($this->attributes['curr_qty'])) {
            return $this->attributes['curr_qty'];
        }

        if (!$this->ignore_cost) {
            if (isset($this->cost->curr_qty) && $this->cost->period_id == $period_id) {
                return $this->cost->curr_qty;
            }
        }

        if (isset($this->calculated['curr_qty'])) {
            return $this->calculated['curr_qty'];
        }

//        return $this->calculated['curr_qty'] = ActualResources::where('breakdown_resource_id', $this->breakdown_resource_id)->where('period_id', $period_id)->sum('qty') ?: 0;
        return $this->calculated['curr_qty'] = $this->actual_resources->where('period_id', $period_id)->sum('qty') ?: 0;
    }

    public function getCurrCostAttribute()
    {
        $period_id = $this->getCalculationPeriod()->id;
        if (!empty($this->attributes['curr_cost'])) {
            return $this->attributes['curr_cost'];
        }

        if (!$this->ignore_cost) {
            if (isset($this->cost->curr_cost) && $this->cost->period_id == $period_id) {
                return $this->cost->curr_cost;
            }
        }

        if (isset($this->calculated['curr_cost'])) {
            return $this->calculated['curr_cost'];
        }

//        return $this->calculated['curr_cost'] = ActualResources::where('breakdown_resource_id', $this->breakdown_resource_id)->where('period_id', $period_id)->sum('cost') ?: 0;
        return $this->calculated['curr_cost'] = $this->actual_resources()->where('period_id', $period_id)->sum('cost') ?: 0;
    }

    public function getCurrUnitPriceAttribute()
    {
        if (!empty($this->attributes['curr_unit_price'])) {
            return $this->attributes['curr_unit_price'];
        }

        if (!$this->ignore_cost) {
            if (isset($this->cost->curr_unit_price) && $this->cost->period_id == $this->getCalculationPeriod()->id) {
                return $this->cost->curr_unit_price;
            }
        }

        if ($this->curr_qty) {
            return $this->curr_cost / $this->curr_qty;
        }

        return 0;
    }

    public function getPrevQtyAttribute()
    {
        $period_id = $this->getCalculationPeriod()->id;
        if (!empty($this->attributes['prev_qty'])) {
            return $this->attributes['prev_qty'];
        }

        if (!$this->ignore_cost) {
            if (isset($this->cost->prev_qty) && $this->cost->period_id == $period_id) {
                return $this->cost->prev_qty;
            }
        }

        if (isset($this->calculated['prev_qty'])) {
            return $this->calculated['prev_qty'];
        }

//        return $this->calculated['prev_qty'] = ActualResources::where('breakdown_resource_id', $this->breakdown_resource_id)->where('period_id', '<', $period_id)->sum('qty') ?: 0;
        return $this->calculated['prev_qty'] = $this->actual_resources->filter(function ($r) use ($period_id) {
            return $r->period_id < $period_id;
        })->sum('qty') ?: 0;
    }

    public function getPrevCostAttribute()
    {
        if (!empty($this->attributes['prev_cost'])) {
            return $this->attributes['prev_cost'];
        }

        $period_id = $this->getCalculationPeriod()->id;
        if (!$this->ignore_cost) {
            if (isset($this->cost->prev_cost) && $this->cost->period_id == $period_id) {
                return $this->cost->prev_cost;
            }
        }

        if (isset($this->calculated['prev_cost'])) {
            return $this->calculated['prev_cost'];
        }

        return $this->calculated['prev_cost'] = $this->actual_resources->filter(function ($r) use ($period_id) {
            return $r->period_id < $period_id;
        })->sum('cost') ?: 0;
    }

    public function getPrevUnitPriceAttribute()
    {
        if (!empty($this->attributes['prev_unit_price'])) {
            return $this->attributes['prev_unit_price'];
        }

        if (!$this->ignore_cost) {
            if (isset($this->cost->prev_unit_price) && $this->cost->period_id == $this->getCalculationPeriod()->id) {
                return $this->cost->prev_unit_price;
            }
        }

        if ($this->prev_qty) {
            return $this->prev_cost / $this->prev_qty;
        }

        return 0;
    }
}