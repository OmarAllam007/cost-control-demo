<?php

namespace App;

use App\Http\Controllers\Reports\ActivityResourceBreakDown;
use Illuminate\Database\Eloquent\Model;

class ActualResources extends Model
{
    protected $fillable = ['breakdown_resource_id', 'period_id', 'original_code', 'qty', 'unit_price', 'cost', 'unit_id', 'action_date'];

    function project()
    {
        return $this->belongsTo(Project::class);
    }

    function wbs()
    {
        return $this->belongsTo(WbsLevel::class, 'wbs_level_id');
    }

    function breakdown_resource()
    {
        return $this->belongsTo(BreakdownResource::class, 'breakdown_resource_id');
    }

    function resource_shadow()
    {
        return $this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id');
    }

    function period_id()
    {
        return $this->belongsTo(Period::class);
    }

    function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    function getProgressAttribute()
    {
        return .8;
    }

    function getPrevQtyAttribute()
    {
        $previousResources = ActualResources::where('id', '<', $this->id)
            ->where('period_id', '!=', $this->period_id)
            ->get();

        $prevQty = 0;
        foreach ($previousResources as $previousResource) {
            $prevQty += $previousResource->qty;
        }

        return $prevQty;
    }

    function getPrevCostAttribute()
    {
        $previousResources = ActualResources::where('id', '<', $this->id)
            ->where('period_id', '!=', $this->period_id)
            ->get();
        $prevCost = 0;

        foreach ($previousResources as $previousResource) {
            $prevCost += $previousResource->cost;
        }

        return $prevCost;

    }


    function getCurrentCostAttribute()
    {
        return $this->cost * $this->qty;
    }

    function getCurrentQtyAttribute()
    {
        return $this->prev_qty + $this->qty;
    }

    function getUpdatedEqvAttribute()
    {
        $updatedResource = ActualResources::where('id', '<', $this->id)
            ->where('breakdown_resource_id', $this->breakdown_resource_id)
            ->where('period_id', $this->period_id)->first();

        if ($updatedResource) {
//            return $this->cost * $this->qty;
            return $updatedResource->cost / $updatedResource->qty;
        }

        return 0;
    }


    function getTotalUpdatedQtyAttribute()
    {
        //get Previous qty
        $updatedResources = ActualResources::where('id', '<', $this->id)
            ->where('breakdown_resource_id', $this->breakdown_resource_id)
            ->where('period_id', '!=', $this->period_id)->get();

        if (count($updatedResources)) {

            $totalQty = 0;

            foreach ($updatedResources as $resource) {
                $totalQty += $resource->qty;
            }
            return $totalQty + $this->qty; // previous + current => 2date
        }

        return $this->qty;
    }

    function getTotalUpdatedCostAttribute()
    {
        $updatedResources = ActualResources::where('id', '<', $this->id)
            ->where('breakdown_resource_id', $this->breakdown_resource_id)
            ->where('period_id', '!=', $this->period_id)->get();

        if (count($updatedResources)) {

            $totalCost = 0;

            foreach ($updatedResources as $resource) {
                $totalCost += $resource->cost;
            }
            return $totalCost + $this->cost;
        }

        return 0;
    }

    function getTotalUpdatedEqvAttribute()
    {

        return number_format($this->total_updated_cost / $this->total_updated_qty, 2);
    }


    function getAllowableEqvCostAttribute()
    {
        $division_name = $this->breakdown_resource->breakdown->std_activity->division->name;
        $budget_cost = $this->breakdown_resource->budget_cost;
        $budget_unit = $this->breakdown_resource->budget_unit;
        if ($division_name == '01.GENERAL REQUIREMENT') {
            return $this->progress * $budget_cost;
        }
        if ($budget_cost == 0) {
            return 0;
        } else {
            if ($this->progress == 1) {
                return $budget_cost;
            } else {
                if ($this->total_updated_cost >= $budget_cost) {
                    return $budget_cost;
                } else if ($this->total_updated_qty > $budget_unit) {
                    return $budget_cost;
                } else {
                    return $this->total_updated_qty * $this->breakdown_resource->resource->rate;
                }

            }

        }


        return 0;
    }


    function getVarianceAttribute()
    {
        return $this->total_updated_eqv * $this->total_updated_cost;
    }


    function getRemainPriceUnitAttribute()
    {
        return number_format($this->updated_eqv, 2);//till change it
    }

    function getRemainQtyAttribute()
    {
        $budget_unit = $this->breakdown_resource->budget_unit;
        if ($budget_unit) {
            if ($this->progress == 1) {
                return 0;
            } else {

                if ($this->total_updated_qty > $budget_unit) {
                    return ($this->total_updated_qty * (1 - $this->progress) / $this->progress);
                } else {
                    return ($budget_unit - $this->total_updated_qty) + 3;
                }
            }


        }
        return 0;
    }

    function getRemainCostAttribute()
    {
        return $this->remain_qty * $this->remain_price_unit;
    }


    function getBlAllowableCostAttribute()
    {
        $budget_cost = $this->breakdown_resource->budget_cost;
        if ($budget_cost) {
            return $budget_cost - $this->allowable_eqv_cost;
        }
        return 0;
    }

    function getVarianceTenAttribute()
    {
        return $this->bla_allowable_cost - $this->remain_qty;
    }


    //@compeletion
    function getCompleteCostAttribute() {
        return $this->remain_cost + $this->total_updated_cost;
    }

    function getCompleteQtyAttribute()
    {
        return $this->remain_qty + $this->total_updated_qty;
    }

    public function getCompletePriceUnitAttribute()
    {
        if ($this->complete_cost) {
            return $this->complete_cost / $this->complete_qty;
        }
        return 0;
    }

    public function getCompleteVarianceAttribute()
    {

        return $this->breakdown_resource->resource->rate * $this->complete_price_unit;
    }

    public function getCompleteQtyVarianceAttribute()
    {
        $budget_unit = $this->breakdown_resource->budget_unit;
        if ($budget_unit) {
            return $budget_unit - $this->complete_qty;
        }
        return 0;
    }

    public function getCompleteCostVarianceAttribute()
    {
        $budget_cost = $this->breakdown_resource->budget_cost;
        if ($budget_cost) {
            return $budget_cost - $this->complete_cost;
        }
        return $budget_cost;
    }

    public function getPhysicalUnitAttribute()
    {
        $resource = $this->breakdown_resource;
        if ($resource->budget_cost) {
            return $this->total_updated_cost / $resource->resource->rate;
        }
        return 0;
    }

    public function getPWIndexAttribute()
    {
        $measure_unit = $this->unit_id;
        $budget_unit = $this->breakdown_resource->budget_unit;
        if ($measure_unit) {

            return ($budget_unit - $this->total_updated_qty) / $budget_unit;
        }
        return 0;
    }




}
