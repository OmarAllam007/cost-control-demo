<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 2/7/18
 * Time: 9:23 AM
 */

namespace App\Rollup\Actions;


use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;

class ActivityRollup
{
    /** @var Project */
    private $project;
    private $codes;
    private $rollup_resource;
    private $now;
    private $user_id;
    private $rollup_shadow;

    public function __construct(Project $project, $codes)
    {
        $this->project = $project;
        $this->codes = $codes;

        $this->now = date('Y-m-d H:i:s');
        $this->user_id = auth()->id() ?: 2;

        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();
    }

    function handle()
    {
        $success = 0;

        foreach ($this->codes as $code) {
            if ($this->rollupActivity($code)) {
                ++$success;
            }
        }

        return $success;
    }

    private function rollupActivity($code)
    {
        $hasRollup = BreakdownResource::where('project_id', $this->project->id)
            ->where(compact('code'))->where('is_rollup', 1)->exists();

        if ($hasRollup) {
            return false;
        }

        $resource = $this->project->shadows()
            ->where('code', $code)
            ->first();

        $this->createRollupShadow($resource);

        BreakdownResource::where('id', '<>', $this->rollup_resource->id)
            ->where('project_id', $this->project->id)->where('code', $code)
            ->update([
                'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_resource->id,
                'updated_by' => $this->user_id, 'updated_at' => $this->now
            ]);

        $this->project->shadows()
            ->where('id', '<>', $this->rollup_shadow->id)
            ->where('code', $code)
            ->update([
                'show_in_cost' => false, 'rolled_up_at' => $this->now,
                'rollup_resource_id' => $this->rollup_shadow->id,
                'updated_by' => $this->user_id, 'updated_at' => $this->now
            ]);

        return true;
    }

    private function createRollupShadow($resource)
    {
        $this->createRollupResource($resource);

        $total_cost = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->where('code', $resource->code)
            ->sum('budget_cost');

        return $this->rollup_shadow = BreakDownResourceShadow::forceCreate([
            'breakdown_resource_id' => $this->rollup_resource->id, 'template_id' => 0,
            'resource_code' => $resource->code, 'resource_type_id' => 4,
            'resource_name' => $resource->activity, 'resource_type' => '07.OTHERS',
            'activity_id' => $resource->activity_id, 'activity' => $resource->activity,
            'eng_qty' => 1, 'budget_qty' => 1, 'resource_qty' => 1, 'budget_unit' => 1,
            'resource_waste' => 0, 'unit_price' => $total_cost, 'budget_cost' => $total_cost,
            'measure_unit' => 'LS', 'unit_id' => 15, 'template' => 'Activity Rollup',
            'breakdown_id' => 0, 'wbs_id' => $resource->wbs_id,
            'project_id' => $resource->project_id, 'show_in_budget' => false, 'show_in_cost' => true,
            'remarks' => 'Cost account rollup', 'productivity_ref' => '', 'productivity_output' => 0,
            'labors_count' => 0, 'boq_equivilant_rate' => 1, 'productivity_id' => 0,
            'code' => $this->rollup_resource->code, 'resource_id' => 0,
            'boq_id' => $resource->boq_id, 'survey_id' => $resource->survey_id,
            'boq_wbs_id' => $breakdown->qty_survey->boq->wbs_id ?? 0, 'survey_wbs_id' => $resource->qs_wbs_id ?? 0,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now, 'is_rollup' => true
        ]);
    }

    private function createRollupResource($resource)
    {
        return $this->rollup_resource  = BreakdownResource::forceCreate([
            'breakdown_id' => 0, 'resource_id' => 0, 'std_activity_resource_id' => 0,
            'productivity_id' => 0, 'budget_qty' => 1, 'eng_qty' => 1, 'remarks' => 'Activity rollup',
            'resource_qty' => 1, 'equation' => 1, 'labor_count' => 0, 'wbs_id' => $resource->wbs_id,
            'project_id' => $resource->project_id, 'code' => $resource->code, 'is_rollup' => true,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now
        ]);
    }


}