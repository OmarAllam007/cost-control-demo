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
        return $this->$this->belongsTo(WbsLevel::class, 'wbs_level_id');
    }

    function breakdown_resource()
    {
        return $this->$this->belongsTo(BreakdownResource::class, 'breakdown_resource_id');
    }

    function resource_shadow()
    {
        return $this->$this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id');
    }

    function period_id()
    {
        return $this->$this->belongsTo(Period::class);
    }

    function unit()
    {
        return $this->$this->belongsTo(Unit::class);
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

        return 0;
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

    function getAllowableEqvCost()
    {
        $division_name = $this->breakdown_resource->breakdown->std_activity->division->name;
        $budget_cost = $this->breakdown_resource->budget_cost;
        $budget_unit = $this->breakdown_resource->budget_unit;

        if ($division_name == '01.GENERAL REQUIREMENT') {
            /*
             * get progress * budget cost
             */

        } else {

            if ($budget_cost == 0) {
                return 0;
            } else {
                /*
                 * if progress == 0 return budget_cost
                 */

                /*
                 * else
                 */
                if ($this->total_updated_cost >= $budget_cost) {
                    return $budget_cost;
                } else if ($this->total_updated_qty > $budget_unit) {
                    return $budget_cost;
                } else {
                    return $this->total_updated_qty * $this->breakdown_resource->rate;
                }
            }
        }

        return 0;
    }


}
