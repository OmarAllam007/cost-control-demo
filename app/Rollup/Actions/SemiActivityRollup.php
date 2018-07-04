<?php

namespace App\Rollup\Actions;


use App\ActualResources;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use App\Unit;
use function collect;
use function compact;
use Illuminate\Support\Collection;

class SemiActivityRollup
{
    /** @var Project */
    private $project;

    /** @var Collection */
    private $codes;
    private $rollup_resource;
    /** @var string */
    private $now;
    private $user_id;
    private $rollup_shadow;

    /** @var Collection */
    private $data;
    private $extra;

    /** @var Collection */
    private $unit_cache;

    public function __construct(Project $project, $data, $extra)
    {
        $this->project = $project;
        $this->data = collect($data);
        $this->codes = $this->data->keys();
        $this->extra = $extra;

        $this->now = date('Y-m-d H:i:s');
        $this->user_id = auth()->id() ?: 2;
        $this->unit_cache = Unit::pluck('type', 'id');

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
        $resources = BreakdownResource::where('project_id', $this->project->id)
            ->where(compact('code'))
            ->whereIn('id', $this->data->get($code))
            ->get();

        if (!$resources->count()) {
            return false;
        }

        $resource = $resources->first();

        $this->createRollupShadow($resource, $code, $resources->pluck('id'));

        BreakdownResource::whereIn('id', $resources->pluck('id'))->update([
            'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_resource->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);

        $this->project->shadows()->whereIn('breakdown_resource_id', $resources->pluck('id'))->update([
            'show_in_cost' => 0, 'rolled_up_at' => $this->now,
            'rollup_resource_id' => $this->rollup_shadow->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);

        $this->handleSummed($resources);
        $this->handleRolled($resources);

        return $resources->count();
    }

    private function createRollupShadow($resource, $code, $resource_ids)
    {
        $this->createRollupResource($resource);
        $total_cost = BreakDownResourceShadow::whereIn('breakdown_resource_id', $resource_ids)->sum('budget_cost') ?: 0;
        $budget_unit = $this->extra['budget_unit'][$code] ?? 1;
        $unit_id = $this->extra['measure_unit'][$code] ?? 15;
        $measure_unit = $this->unit_cache->get($unit_id);
        $unit_price = $total_cost / $budget_unit;
        $remarks = $this->extra['remarks'][$resource->code] ?? 'Semi Activity rollup';

        $cost_account_suffix = '01';
        $latest_activity = BreakDownResourceShadow::where(compact('code'))
            ->where('is_rollup', true)->max('resource_code');

        if ($latest_activity) {
            $last_dot = strrpos($latest_activity, '.') + 1;
            $suffix = intval(substr($latest_activity, $last_dot)) + 1;
            $cost_account_suffix = sprintf('%02d', $suffix);
        }

        $important = BreakDownResourceShadow::whereIn('breakdown_resource_id', $resource_ids)->where('important', 1)->exists();

        $resource_code = $this->extra['resource_codes'][$code] ?? $resource->code . '.' . $cost_account_suffix;
        $resource_name = $this->extra['resource_names'][$code] ?? $resource->shadow->activity;
        $types = BreakDownResourceShadow::whereIn('breakdown_resource_id', $resource_ids)->pluck('resource_type', 'resource_type_id');
        if ($types->count() == 1) {
            $resource_type = $types->values()->first();
            $resource_type_id = $types->keys()->first();
        } else {
            $resource_type = '04.Subcontractors';
            $resource_type_id = 4;
        }

        $this->rollup_shadow = BreakDownResourceShadow::forceCreate([
            'breakdown_resource_id' => $this->rollup_resource->id, 'template_id' => 0,
            'resource_code' => $resource_code,
            'resource_type_id' => $resource_type_id,
            'cost_account' => $resource->code . '.' . $cost_account_suffix,
            'resource_name' => $resource_name, 'resource_type' => $resource_type,
            'activity_id' => $resource->shadow->activity_id, 'activity' => $resource->shadow->activity,
            'eng_qty' => $budget_unit, 'budget_qty' => $budget_unit, 'resource_qty' => $budget_unit, 'budget_unit' => $budget_unit,
            'resource_waste' => 0, 'unit_price' => $unit_price, 'budget_cost' => $total_cost,
            'measure_unit' => $measure_unit, 'unit_id' => $unit_id, 'template' => 'Semi Activity Rollup',
            'breakdown_id' => 0, 'wbs_id' => $resource->wbs_id,
            'project_id' => $resource->project_id, 'show_in_budget' => false, 'show_in_cost' => true,
            'remarks' => $remarks, 'productivity_ref' => '', 'productivity_output' => 0,
            'labors_count' => 0, 'boq_equivilant_rate' => 1, 'productivity_id' => 0,
            'code' => $this->rollup_resource->code, 'resource_id' => 0,
            'boq_id' => $resource->shadow->boq_id, 'survey_id' => $resource->shadow->survey_id,
            'boq_wbs_id' => $resource->shadow->boq->wbs_id ?? 0, 'survey_wbs_id' => $resource->shadow->qs_wbs_id ?? 0,
            'important' => $important, 'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now, 'is_rollup' => true
        ]);

        return $this->update_cost($code, $resource_ids);
    }

    private function createRollupResource($resource)
    {
        $remarks = $this->extra['remarks'][$resource->code] ?? 'Semi Activity rollup';

        return $this->rollup_resource = BreakdownResource::forceCreate([
            'breakdown_id' => 0, 'resource_id' => 0, 'std_activity_resource_id' => 0,
            'productivity_id' => 0, 'budget_qty' => 1, 'eng_qty' => 1, 'remarks' => $remarks,
            'resource_qty' => 1, 'equation' => 1, 'labor_count' => 0, 'wbs_id' => $resource->wbs_id,
            'project_id' => $resource->project_id, 'code' => $resource->code, 'is_rollup' => true,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now
        ]);
    }

    private function update_cost($code, $resource_ids)
    {
        $actual_resources = BreakDownResourceShadow::whereIn('breakdown_resource_id', $resource_ids)->get();

        $period = $this->project->open_period();
        if (!$period) {
            return $this->rollup_shadow;
        }

        $to_date_cost = $actual_resources->sum('to_date_cost');
        $to_date_qty = $this->extra['to_date_qty'][$code] ?? 0;
        $to_date_unit_price = 0;

        if ($to_date_qty) {
            $to_date_unit_price = $to_date_cost / $to_date_qty;
        }

        $progress = min(100, $this->extra['progress'][$code] ?? 0);
        if (!$to_date_cost) {
            $progress = 0;
        }

        $status = 'Not Started';
        if ($progress) {
            $status = $progress < 100 ? 'In Progress' : 'Closed';
            $this->rollup_shadow->update(compact('progress', 'status'));
        }

        ActualResources::forceCreate([
            'project_id' => $this->project->id, 'wbs_level_id' => $this->rollup_shadow->wbs_id, 'breakdown_resource_id' => $this->rollup_resource->id,
            'qty' => $to_date_qty, 'cost' => $to_date_cost, 'unit_price' => $to_date_unit_price,
            'unit_id' => $this->rollup_shadow->unit_id, 'action_date' => $this->now, 'resource_id' => $this->rollup_shadow->resource_id,
            'user_id' => auth()->id(), 'batch_id' => 0, 'period_id' => $period->id, 'progress' => $progress, 'status' => $status,
        ]);

        ActualResources::whereIn('id', $actual_resources->pluck('id'))->where('period_id', $period->id)->delete();

        return $this->rollup_shadow;
    }

    /**
     * @param Collection $resources
     */
    private function handleSummed($resources)
    {
        $this->project->shadows()
            ->whereIn('breakdown_resource_id', $resources->pluck('id'))
            ->where('is_sum', true)->get()->each(function ($resource) {
                $query = $this->project->shadows()->where('code', $this->rollup_shadow->code)
                    ->where('resource_id', $resource->resource_id);

                $query->update([
                    'show_in_cost' => 0, 'rolled_up_at' => $this->now,
                    'rollup_resource_id' => $this->rollup_shadow->id,
                    'updated_by' => $this->user_id, 'updated_at' => $this->now, 'summed_at' => null
                ]);

                BreakdownResource::whereIn('id', $query->pluck('breakdown_resource_id'))->update([
                    'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_resource->id,
                    'updated_by' => $this->user_id, 'updated_at' => $this->now
                ]);

                $resource->breakdown_resource->delete();
                $resource->delete();
            });
    }

    /**
     * @param $resources
     */
    private function handleRolled($resources)
    {
        $this->project->shadows()
            ->whereIn('breakdown_resource_id', $resources->pluck('id'))
            ->where('is_rollup', true)->get()->each(function ($resource) {
                $query = $this->project->shadows()->where('rollup_resource_id', $resource->id);
                $query->update([
                    'show_in_cost' => 0, 'rolled_up_at' => $this->now,
                    'rollup_resource_id' => $this->rollup_shadow->id,
                    'updated_by' => $this->user_id, 'updated_at' => $this->now
                ]);

                BreakdownResource::whereIn('id', $query->pluck('breakdown_resource_id'))->update([
                    'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_resource->id,
                    'updated_by' => $this->user_id, 'updated_at' => $this->now
                ]);

                $resource->breakdown_resource->delete();
                $resource->delete();
            });;
    }
}