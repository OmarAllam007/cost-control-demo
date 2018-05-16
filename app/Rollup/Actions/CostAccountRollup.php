<?php

namespace App\Rollup\Actions;


use App\ActualResources;
use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use App\Unit;
use Carbon\Carbon;
use function compact;
use Illuminate\Support\Collection;

class CostAccountRollup
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
//            // Do not select cost accounts with rolled resources
//            ->doesntHave('rolled_resources')
            ->get()
            ->each(function ($breakdown) {
                $this->rollupBreakdown($breakdown);
            });

        return $breakdowns->count();
    }

    private function rollupBreakdown($breakdown)
    {
        $this->createRollupShadow($breakdown);

        $breakdown->resources()->where('id', '<>', $this->rollup_resource->id)->where('is_rollup')->delete();

        $breakdown->resources()->where('id', '<>', $this->rollup_resource->id)->update([
            'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_resource->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);

        $breakdown->shadows()->where('is_rollup', true)->where('id', '<>', $this->rollup_shadow->id)->delete();
        BreakDownResourceShadow::where('breakdown_id', $breakdown->id)->where('id', '<>', $this->rollup_shadow->id)->update([
            'show_in_cost' => false, 'rolled_up_at' => $this->now, 'rollup_resource_id' => $this->rollup_shadow->id,
            'updated_by' => $this->user_id, 'updated_at' => $this->now
        ]);

        $breakdown->rolled_up_at = $this->now;
        $breakdown->save();
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

        $shadows = BreakDownResourceShadow::with('breakdown_resource')
            ->where('breakdown_id', $breakdown->id)
            ->canBeRolled()->get();

        $total_cost =  $shadows->sum('budget_cost'); //$breakdown->resources->pluck('shadow')->sum('budget_cost');
        $budget_unit = $this->extra['budget_unit'][$breakdown->id] ?? 1;
        $unit_id = $this->extra['measure_unit'][$breakdown->id] ?? 15;
        $measure_unit = $this->unit_cache->get($unit_id);
        $unit_price = $total_cost / $budget_unit;

        $this->rollup_shadow = $this->rollup_shadow = BreakDownResourceShadow::forceCreate([
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
            'created_by' => $this->user_id, 'created_at' => $this->now, 'is_rollup' => true, 'cost_account' => $breakdown->cost_account
        ]);

        return $this->update_cost($breakdown, $shadows->pluck('breakdown_resource'));
    }

    /**
     * @param $breakdown
     * @return BreakDownResourceShadow
     */
    private function update_cost($breakdown, $resources): BreakDownResourceShadow
    {
        $actual_resources = ActualResources::whereIn('breakdown_resource_id', $resources->pluck('id'))->get();

        $period = $this->project->open_period();
        if (!$period) {
            // If no open period select the last period in the project to apply
            $period = $this->project->periods()->latest('id')->first();

            // If there is no period at all in the project then ignore to date values as it is pointless
            if (!$period) {
                return $this->rollup_shadow;
            }
        }

        // Update actual resource data based on to date quantity
        $to_date_cost = $actual_resources->sum('cost');
        $to_date_qty = $this->extra['to_date_qty'][$breakdown->id] ?? 0;
        $to_date_unit_price = 0;
        $progress = 0;
        $status = 'Not Started';

        if ($to_date_qty) {
            $to_date_unit_price = $to_date_cost / $to_date_qty;
            $progress = min(100, $to_date_qty * 100 / $this->rollup_shadow->budget_unit);
            $status = $progress < 100 ? 'In Progress' : 'Closed';
        }

        ActualResources::forceCreate([
            'project_id' => $this->project->id, 'wbs_level_id' => $this->rollup_shadow->wbs_id, 'breakdown_resource_id' => $this->rollup_resource->id,
            'qty' => $to_date_qty, 'cost' => $to_date_cost, 'unit_price' => $to_date_unit_price,
            'unit_id' => $this->rollup_shadow->unit_id, 'action_date' => $this->now, 'resource_id' => $this->rollup_shadow->resource_id,
            'user_id' => auth()->id(), 'batch_id' => 0, 'period_id' => $period->id, 'progress' => $progress, 'status' => $status,
        ]);

        $this->rollup_shadow->update(compact('progress', 'status'));

        ActualResources::whereIn('id', $actual_resources->pluck('id'))->where('period_id', $period->id)->delete();

        return $this->rollup_shadow;
    }
}