<?php
namespace App\Behavior;

use App\BreakDownResourceShadow;

trait RollupResource
{

    function getTopMaterialAttribute()
    {
        if ($this->is_rollup) {
            return '';
        }

        return $this->resource->top_material;
    }

    function calculateProgress()
    {
        if ($this->budget_unit) {
            $progress = min(100, $this->to_date_qty * 100 / $this->budget_unit);

            if ($progress < 100 && !$this->std_activity->isGeneral()) {
                return $progress;
            }
        }

        return floatval($this->progress);
    }

    function scopeCostOnly($query)
    {
        $query->where('show_in_cost', true);
    }

    function scopeBudgetOnly($query)
    {
        $query->where('show_in_budget', true);
    }

    function scopeCurrentOnly($query, $period)
    {
        return $query->whereRaw("breakdown_resource_id in (select breakdown_resource_id from actual_resources where period_id = {$period->id})");
    }

    function rollupResource()
    {
        return $this->belongsTo(BreakDownResourceShadow::class);
    }

    function isActivityRollup()
    {
        return $this->is_rollup && $this->resource_code == $this->code;
    }

    function isCostAccountRollup()
    {
        return $this->is_rollup && $this->resource_code == $this->cost_account;
    }

    function isResourceRollup()
    {
        return $this->is_rollup && !$this->isActivityRollup() && !$this->isCostAccountRollup();
    }
}