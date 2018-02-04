<?php

namespace App\Rollup\Actions;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;

class ImportantResourcesRollup
{
    /** @var Project */
    private $project;

    /** @var Collection */
    private $data;

    private $now;
    private $user_id;

    private $rollup_resource;
    private $rollup_shadow;

    function __construct(Project $project, $data = [])
    {
        $this->project = $project;
        $this->data = collect($data);

        $this->now = date('Y-m-d H:i:s');
        $this->user_id = auth()->id() ?? 0;

        Breakdown::flushEventListeners();
        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();
    }

    function handle()
    {
        $cost_accounts = Breakdown::where('project_id', $this->project->id)
            ->find($this->data->keys()->toArray())->each(function ($breakdown) {
                $this->rollupBreakdown($breakdown);
            })->count();

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

        $total_cost = BreakDownResourceShadow::whereIn('breakdown_resource_id', $resource_ids)->sum('budget_cost');

        return $this->rollup_shadow = BreakDownResourceShadow::forceCreate([
            'breakdown_resource_id' => $this->rollup_resource->id, 'template_id' => 0,
            'resource_code' => $breakdown->cost_account, 'resource_type_id' => 3,
            'resource_name' => $breakdown->qty_survey->description, 'resource_type' => '03.MATERIAL',
            'activity_id' => $breakdown->std_activity_id, 'activity' => $breakdown->std_activity->name,
            'eng_qty' => 1, 'budget_qty' => 1, 'resource_qty' => 1, 'budget_unit' => 1,
            'resource_waste' => 0, 'unit_price' => $total_cost, 'budget_cost' => $total_cost,
            'measure_unit' => 'LM', 'unit_id' => 3, 'template' => 'Cost Account Rollup',
            'breakdown_id' => $breakdown->id, 'wbs_id' => $breakdown->wbs_level_id,
            'project_id' => $breakdown->project_id, 'show_in_budget' => false, 'show_in_cost' => true,
            'remarks' => 'Cost account rollup', 'productivity_ref' => '', 'productivity_output' => 0,
            'labors_count' => 0, 'boq_equivilant_rate' => 1, 'productivity_id' => 0,
            'code' => $this->rollup_resource->code, 'resource_id' => 0,
            'boq_id' => $breakdown->qty_survey->boq->id ?? 0, 'survey_id' => $breakdown->qty_survey->id ?? 0,
            'boq_wbs_id' => $breakdown->qty_survey->boq->wbs_id ?? 0, 'survey_wbs_id' => $breakdown->qty_survey->wbs_level_id ?? 0,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now, 'is_rollup' => true
        ]);
    }

    private function createRollupResource($breakdown)
    {
        $code = $breakdown->resources()->first()->code;

        return $this->rollup_resource  = BreakdownResource::forceCreate([
            'breakdown_id' => $breakdown->id, 'resource_id' => 0, 'std_activity_resource_id' => 0,
            'productivity_id' => 0, 'budget_qty' => 1, 'eng_qty' => 1, 'remarks' => 'Resources rollup',
            'resource_qty' => 1, 'equation' => 1, 'labor_count' => 0, 'wbs_id' => $breakdown->wbs_level_id,
            'project_id' => $breakdown->project_id, 'code' => $code, 'is_rollup' => true,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now
        ]);
    }


}