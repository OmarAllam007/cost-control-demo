<?php

namespace App\Rollup\Actions;

use App\ActualResources;
use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\Unit;
use Illuminate\Support\Collection;

class ImportantResourcesRollup
{
    //<editor-fold defaultstate="collapsed" desc="Variable definitions">
    /** @var Project */
    private $project;

    /** @var Collection */
    private $data;

    private $now;
    private $user_id;

    private $rollup_resource;
    private $rollup_shadow;

    /** @var array */
    private $extra;

    /** @var Collection */
    private $unit_cache;

    function __construct(Project $project, $data = [], $extra = [])
    {
        $this->project = $project;
        $this->data = collect($data);
        $this->now = date('Y-m-d H:i:s');
        $this->user_id = auth()->id() ?? 0;
        $this->extra = $extra;
        $this->unit_cache = Unit::pluck('type', 'id');

        Breakdown::flushEventListeners();
        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();
    }
    //</editor-fold>

    function handle()
    {
        $this->data->chunk(500)->each(function($data) {
            Breakdown::where('project_id', $this->project->id)
                ->whereRaw("id in (select breakdown_id from break_down_resource_shadows where project_id = {$this->project->id})")
                // Do not select already rolled up cost accounts
                ->whereNull('rolled_up_at')
                // Do not select cost accounts with rolled resources
                ->doesntHave('rolled_resources')
                ->whereIn('id', $data->keys())
                ->get()
                ->each(function ($breakdown) {
                    $this->rollupBreakdown($breakdown);
                });
        });

        $cost_accounts = $this->data->count();
        $resources = $this->data->flatten()->count();

        return compact('resources', 'cost_accounts');
    }

    private function rollupBreakdown($breakdown)
    {
        $resource_ids = $this->data->get($breakdown->id);
        $this->createRollupShadow($breakdown, $resource_ids);

        $breakdown->resources()->whereIn('id', $resource_ids)->update([
            'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_resource->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);

        BreakDownResourceShadow::where('breakdown_id', $breakdown->id)
            ->whereIn('breakdown_resource_id', $resource_ids)->update([
                'show_in_cost' => false, 'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_shadow->id,
                'updated_by' => $this->user_id, 'updated_at' => $this->now
            ]);
    }

    private function createRollupShadow($breakdown, $resource_ids)
    {
        $this->createRollupResource($breakdown);

        $total_cost = BreakDownResourceShadow::whereIn('breakdown_resource_id', $resource_ids)->sum('budget_cost') ?: 0;
        $budget_unit = $this->extra['budget_unit'][$breakdown->id] ?? 1;
        $unit_id = $this->extra['measure_unit'][$breakdown->id] ?? 3;
        $measure_unit = $this->unit_cache->get($unit_id);
        $unit_price = $total_cost / $budget_unit;

        $resource = $this->rollup_shadow = BreakDownResourceShadow::forceCreate([
            'breakdown_resource_id' => $this->rollup_resource->id, 'template_id' => 0,
            'resource_code' => $breakdown->cost_account, 'resource_type_id' => 3,
            'resource_name' => $breakdown->qty_survey->description, 'resource_type' => '03.MATERIAL',
            'activity_id' => $breakdown->std_activity_id, 'activity' => $breakdown->std_activity->name,
            'eng_qty' => $budget_unit, 'budget_qty' => $budget_unit, 'resource_qty' => $budget_unit, 'budget_unit' => $budget_unit,
            'resource_waste' => 0, 'unit_price' => $unit_price, 'budget_cost' => $total_cost,
            'measure_unit' => $measure_unit, 'unit_id' => $unit_id, 'template' => 'Semi-activity rollup',
            'breakdown_id' => $breakdown->id, 'wbs_id' => $breakdown->wbs_level_id,
            'project_id' => $breakdown->project_id, 'show_in_budget' => false, 'show_in_cost' => true,
            'remarks' => 'Semi-activity rollup', 'productivity_ref' => '', 'productivity_output' => 0,
            'labors_count' => 0, 'boq_equivilant_rate' => 1, 'productivity_id' => 0,
            'code' => $this->rollup_resource->code, 'resource_id' => 0,
            'boq_id' => $breakdown->qty_survey->boq->id ?? 0, 'survey_id' => $breakdown->qty_survey->id ?? 0,
            'boq_wbs_id' => $breakdown->qty_survey->boq->wbs_id ?? 0, 'survey_wbs_id' => $breakdown->qty_survey->wbs_level_id ?? 0,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now, 'is_rollup' => true
        ]);

        $period = $this->project->open_period();
        if (!$period) {
            ActualResources::whereIn('breakdown_resource_id', $resource_ids)->where('period_id', $period->id)->delete();
            CostShadow::whereIn('breakdown_resource_id', $resource_ids)->where('period_id', $period->id)->delete();

            ActualResources::forceCreate([
                'project_id' => $this->project->id, 'wbs_level_id' => $resource->wbs_id,
                'breakdown_resource_id' => $this->rollup_resource->id,
            ]);
        }

        return $resource;
    }

    private function createRollupResource($breakdown)
    {
        $code = $breakdown->resources()->first()->code;
        $budget_unit = $this->extra['budget_unit'][$breakdown->id] ?? 1;

        return $this->rollup_resource  = BreakdownResource::forceCreate([
            'breakdown_id' => $breakdown->id, 'resource_id' => 0, 'std_activity_resource_id' => 0,
            'productivity_id' => 0, 'budget_qty' => $budget_unit, 'eng_qty' => $budget_unit, 'remarks' => 'Semi-activity rollup',
            'resource_qty' => $budget_unit, 'equation' => $budget_unit, 'labor_count' => 0, 'wbs_id' => $breakdown->wbs_level_id,
            'project_id' => $breakdown->project_id, 'code' => $code, 'is_rollup' => true,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now
        ]);
    }


}