<?php

namespace App\Rollup\Actions;


use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BreakdownRollup
{
    //<editor-fold defaultstate="collapsed" desc="Variables definitions">
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

    /** @var Collection */
    private $unit_cache;

    /** @var array */
    private $extra;

    public function __construct(Project $project, $cost_accounts = [], $extra = [])
    {
        $this->project = $project;
        $this->cost_accounts = $cost_accounts;
        $this->extra = $extra;
        $this->user_id = auth()->id() ?: 2;
        $this->now = Carbon::now()->format('Y-m-d H:i:s');
        $this->unit_cache = Unit::pluck('type', 'id');

        Breakdown::flushEventListeners();
        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();
    }
    //</editor-fold>

    function handle()
    {
        $breakdowns = Breakdown::whereIn('id', $this->cost_accounts)
            ->whereRaw("id in (select breakdown_id from break_down_resource_shadows where project_id = {$this->project->id})")
            // Do not select already rolled up cost accounts
            ->whereNull('rolled_up_at')
            // Do not select cost accounts with rolled resources
            ->doesntHave('rolled_resources')
            ->get()
            ->each(function ($breakdown) {
                $this->rollupBreakdown($breakdown);
            });

        return $breakdowns->count();
    }

    private function rollupBreakdown($breakdown)
    {
        $breakdown->rolled_up_at = $this->now;
        $breakdown->save();

        $this->createRollupShadow($breakdown);

        $breakdown->resources()->where('id', '<>', $this->rollup_resource->id)->update([
            'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_resource->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);

        BreakDownResourceShadow::where('breakdown_id', $breakdown->id)->where('id', '<>', $this->rollup_shadow->id)->update([
            'show_in_cost' => false, 'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_shadow->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);
    }

    private function createRollupResource($breakdown)
    {
        $code = $breakdown->resources()->first()->code;
        $budget_unit = $this->extra['budget_unit'][$breakdown->id] ?? 0;

        return $this->rollup_resource  = BreakdownResource::forceCreate([
            'breakdown_id' => $breakdown->id, 'resource_id' => 0, 'std_activity_resource_id' => 0,
            'productivity_id' => 0, 'budget_qty' => $budget_unit, 'eng_qty' => $budget_unit,
            'remarks' => 'Cost account rollup',
            'resource_qty' => $budget_unit, 'equation' => $budget_unit,
            'labor_count' => 0, 'wbs_id' => $breakdown->wbs_level_id,
            'project_id' => $breakdown->project_id, 'code' => $code, 'is_rollup' => true,
            'updated_by' => $this->user_id, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'created_at' => $this->now
        ]);
    }

    private function createRollupShadow($breakdown)
    {
        $this->createRollupResource($breakdown);

        $total_cost = $breakdown->resources->pluck('shadow')->sum('budget_cost');
        $budget_unit = $this->extra['budget_unit'][$breakdown->id] ?? 1;
        $unit_id = $this->extra['measure_unit'][$breakdown->id] ?? 3;
        $measure_unit = $this->unit_cache->get($unit_id);
        $unit_price = $total_cost / $budget_unit;

        return $this->rollup_shadow = BreakDownResourceShadow::forceCreate([
            'breakdown_resource_id' => $this->rollup_resource->id, 'template_id' => 0,
            'resource_code' => $breakdown->cost_account, 'resource_type_id' => 4,
            'resource_name' => $breakdown->qty_survey->description, 'resource_type' => '04.Subcontractors',
            'activity_id' => $breakdown->std_activity_id, 'activity' => $breakdown->std_activity->name,
            'eng_qty' => $budget_unit, 'budget_qty' => $budget_unit, 'resource_qty' => $budget_unit, 'budget_unit' => $budget_unit,
            'resource_waste' => 0, 'unit_price' => $unit_price, 'budget_cost' => $total_cost,
            'measure_unit' => $measure_unit, 'unit_id' => $unit_id, 'template' => 'Cost Account Rollup',
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
}