<?php

namespace App\Support;

use App\BreakDownResourceShadow;
use App\StoreResource;
use App\WbsLevel;
use Illuminate\Support\Collection;

class ActivityLog
{
    /** @var WbsLevel */
    private $wbs;

    /** @var string */
    private $code;
    /** @var Collection */
    private $resourceLogs;

    /**
     * ActivityLog constructor.
     *
     * @param WbsLevel $wbs
     * @param string $code
     */
    public function __construct($wbs, $code)
    {
        $this->code = $code;
        $this->wbs = $wbs;
    }

    function handle()
    {
        $isActivityRollup = BreakDownResourceShadow::where('wbs_id', $this->wbs->id)
            ->where('code', $this->code)->where('is_rollup', true)
            ->whereRaw('code = resource_code')->exists();

        /** @var Collection $shadows */
        $shadows = BreakDownResourceShadow::where('wbs_id', $this->wbs->id)
            ->where('code', $this->code)
            ->where('resource_id', '<>', 0)
            ->when($isActivityRollup, function($q) {
                return $q->where('important', 1);
            })->when(!$isActivityRollup, function($q) {
                return $q->where('show_in_cost', 1);
            })->with(['important_actual_resources', 'actual_resources'])
            ->get();

        $resource_ids = $shadows->pluck('resource_id', 'resource_id');

        $store_resources = StoreResource::where('budget_code', $this->code)
            ->whereIn('resource_id', $resource_ids)->whereNull('row_ids')
            ->get()->groupBY('resource_id');

        $budget_resources = $shadows->groupBy('resource_id');

        /** @var Collection $resourceLogs */
        $this->resourceLogs = $resource_ids->map(function($id) use ($budget_resources, $store_resources) {
            $budget = $budget_resources->get($id);
            $resource = $budget->first();
            $allowable = $budget_resources->flatten()->sum('allowable_ev_cost');
            $cost = $budget_resources->flatten()->sum('to_date_cost');
            $variance = $budget_resources->flatten()->sum('allowable_var');
            $qty_var = $budget_resources->flatten()->sum('to_date_qty_var');
            $important = $budget_resources->flatten()->filter(function($resource) {
                return $resource->important;
            })->count();

            return [
                'name' => $resource->resource_name, 'code' => $resource->resource_code,
                'budget_resources' => $budget, 'rollup' => false,
                'store_resources' => $store_resources->get($id, collect()),
                'allowable' => $allowable, 'cost' => $cost, 'cost_var' => $variance,
                'qty_var' => $qty_var, 'important' => $important > 0
            ];
        })->filter(function ($log) {
            return $log['store_resources']->count();
        });

        // Rollup resources
        $rollupLogs = $this->getRollupLogs();

        return $this->resourceLogs->merge($rollupLogs)->values();
    }

    /**
     * @return mixed
     */
    private function getRollupLogs()
    {
        $rollupLogs = BreakDownResourceShadow::where('wbs_id', $this->wbs->id)
            ->with(['actual_resources'])->where('is_rollup', true)
            ->where('code', $this->code)->get()->map(function ($resource) {
                $budget_resources = BreakDownResourceShadow::where('rollup_resource_id', $resource->id)->get();
                $store_resources = StoreResource::whereIn('actual_resource_id', $resource->actual_resources->pluck('id'))
                    ->whereNull('row_ids')->get();
                $important = $budget_resources->filter(function($resource) {
                    return $resource->important;
                })->count();

                return [
                    'name' => $resource->resource_name, 'code' => $resource->resource_code,
                    'budget_resources' => $budget_resources, 'store_resources' => $store_resources,
                    'actual_resources' => $resource->actual_resources,
                    'rollup' => true, 'rollup_resource' => $resource, 'important' => $important > 0,
                    'allowable' => $resource->allowable_ev_cost, 'cost' => $resource->to_date_cost, 'cost_var' => $resource->allowable_var,
                    'allowable_qty' => $resource->allowable_qty, 'qty_var' => $resource->to_date_qty_var
                ];
            });
        return $rollupLogs;
    }
}