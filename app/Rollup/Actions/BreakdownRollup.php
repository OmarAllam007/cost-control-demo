<?php

namespace App\Rollup\Actions;


use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use Carbon\Carbon;

class BreakdownRollup
{
    /** @var Project */
    private $project;

    /** @var array */
    private $cost_accounts;

    /** @var string */
    private $now;

    /** @var BreakdownResource */
    private $rollup_resource;

    /** @var BreakDownResourceShadow */
    private $rollup_shadow;

    /** @var int */
    private $user_id;

    public function __construct(Project $project, $cost_accounts = [])
    {
        $this->project = $project;
        $this->cost_accounts = $cost_accounts;
        $this->user_id = auth()->id() ?: 2;

        $this->now = Carbon::now()->format('Y-m-d H:i:s');
        Breakdown::flushEventListeners();
        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();
    }

    function handle()
    {
        return Breakdown::where('project_id', $this->project->id)
//            ->with('resources.shadow')
            ->find($this->cost_accounts)->each(function ($breakdown) {
                $this->rollupBreakdown($breakdown);
            })->count();
    }

    private function rollupBreakdown($breakdown)
    {
        $breakdown->rolled_up_at = $this->now;
        $breakdown->save();

        $this->createRollupShadow($breakdown);

        $breakdown->resources()->update([
            'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_resource->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);

        BreakDownResourceShadow::where('breakdown_id', $breakdown->id)->update([
            'show_in_cost' => false, 'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_shadow->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);
    }

    private function createRollupResource($breakdown)
    {
        $code = $breakdown->resources()->first()->code;

        return $this->rollup_resource  = BreakdownResource::forceCreate([
            'breakdown_id' => $breakdown->id, 'resource_id' => 0, 'std_activity_resource_id' => 0,
            'productivity_id' => 0, 'budget_qty' => 1, 'eng_qty' => 1, 'remarks' => 'Cost account rollup',
            'resource_qty' => 1, 'equation' => 1, 'labor_count' => 0, 'wbs_id' => $breakdown->wbs_level_id,
            'project_id' => $breakdown->project_id, 'code' => $code,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now
        ]);
    }

    private function createRollupShadow($breakdown)
    {
        $this->createRollupResource($breakdown);

        $total_cost = $breakdown->resources->pluck('shadow')->sum('budget_cost');

        return $this->rollup_shadow = BreakDownResourceShadow::forceCreate([
            'breakdown_resource_id' => $this->rollup_resource->id, 'template_id' => 0,
            'resource_code' => $breakdown->cost_account, 'resource_type_id' => 4,
            'resource_name' => $breakdown->qty_survey->description, 'resource_type' => '04.Subcontractors',
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
            'created_by' => $this->user_id, 'created_at' => $this->now
        ]);
    }
}