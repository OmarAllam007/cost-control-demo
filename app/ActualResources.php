<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Observers\ActualResourceObserver;
use function GuzzleHttp\json_decode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActualResources extends Model
{
    use HasChangeLog;

    protected $fillable = [
        'project_id', 'wbs_level_id', 'breakdown_resource_id', 'period_id', 'original_code',
        'qty', 'unit_price', 'cost', 'unit_id', 'action_date', 'resource_id', 'batch_id', 'doc_no', 'original_data'
    ];

    protected $casts = ['original_data' => 'array'];

    protected $dates = ['created_at', 'update_at', 'action_date'];

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

    function period()
    {
        return $this->belongsTo(Period::class);
    }

    function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    function resource()
    {
        return $this->belongsTo(Resources::class);
    }

    function budget()
    {
        return $this->belongsTo(BreakDownResourceShadow::class, 'breakdown_resource_id', 'breakdown_resource_id');
    }

    function scopeJoinBudget(Builder $query, WbsLevel $wbs)
    {
        $wbs_ids = $wbs->getChildrenIds();

        $query->getQuery()
            ->selectRaw('ar.*, sh.*')
            ->join('breakdown_resources_shadow sh', 'ar.breakdown_resource_id', '=', 'sh.breakdown_resource_id')
            ->from('actual_resource ar')
            ->whereIn('ar.wbs_level_id', $wbs_ids);
    }

    protected static function boot()
    {
        self::observe(ActualResourceObserver::class);
        parent::boot();
    }

    function toActivityLog()
    {
        $attributes = [
            'budget_resource_name' => $this->budget->resource_name, 'doc_no' => $this->doc_no,
            'budget_measure_unit' => $this->budget->measure_unit,
        ];

        if ($this->original_data) {
            $original_data = json_decode($this->original_data, true);
            $attributes['qty'] = number_format(floatval($original_data[4]), 2);
            $attributes['unit_price'] = number_format(floatval($original_data[5]), 2);
            $attributes['cost'] = number_format(floatval($original_data[6]), 2);
            $attributes['store_measure_unit'] = $original_data[3];
            $attributes['store_resource_name'] = trim($original_data[2]);
        } else {
            $attributes['qty'] = number_format($this->qty, 2);
            $attributes['unit_price'] = number_format($this->unit_price, 2);
            $attributes['cost'] = number_format($this->cost, 2);
            $attributes['store_measure_unit'] = $this->budget->measure_unit;
            $attributes['store_resource_name'] = trim($this->budget->resource_name);
        }

        if ($this->attributes['action_date']) {
            $attributes['action_date'] = $this->action_date->format('d/m/Y');
        } elseif ($this->attributes['created_at']) {
            $attributes['action_date'] = $this->created_at->format('d/m/Y');
        } else {
            $attributes['action_date'] = '';
        }

        return $attributes;
    }


    /*
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
*/
}
