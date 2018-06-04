<?php

namespace App\Rollup\Actions;

use App\ActualResources;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\WbsLevel;
use Carbon\Carbon;

class SumActivity
{
    /** @var WbsLevel */
    private $wbs;

    function __construct(WbsLevel $wbs)
    {
        $this->wbs = $wbs;
        $this->now = Carbon::now()->format('Y-m-d H:i:s');
        $this->user_id = auth()->id() ?: 0;
    }

    function handle()
    {
        BreakdownResource::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();

        BreakDownResourceShadow::whereIn('wbs_id', $this->wbs->getChildrenIds())
            ->where('show_in_budget', 1)->selectRaw('DISTINCT code')
            ->pluck('code')->each(function ($code) {
                $this->handleCode($code);
            });
    }

    private function handleCode($code)
    {
        BreakdownResource::where(compact('code'))
            ->selectRaw('resource_id, count(*) as c')->groupBy('resource_id')->having('c', '>', 1)
            ->pluck('resource_id')->each(function ($resource_id) use ($code) {
                $this->sumResources($code, $resource_id);
            });
    }

    private function sumResources($code, $resource_id)
    {
        $resources = BreakdownResource::where(compact('resource_id', 'code'))
//            ->where('wbs_id', $this->wbs->id)
            ->whereHas('shadow', function ($q) {
                $q->where('show_in_budget', 1);
            })->with('shadow')->get();

        $alreadySummed = BreakDownResourceShadow::where('code', $code)
            ->where('resource_id', $resource_id)->where('is_sum', true)->exists();

        if ($alreadySummed) {
            return 0;
        }

        $shadows = $resources->pluck('shadow');
        $firstShadow = $shadows->first();

        $remarks = $resources->pluck('remarks')->unique()->implode(', ');
        $important = $resources->where('important', 1, false)->count() > 0;
        $labor_count = $resources->sum('labor_count');
        $budget_qty = $resources->first()->budget_qty;
        $eng_qty = $resources->first()->eng_qty;
        $budget_unit = $shadows->sum('budget_unit');
        $budget_cost = $shadows->sum('budget_cost');

        $closed_progress = $shadows->count() * 100;
        $progress = $shadows->sum('progress');

        if ($progress == 0) {
            $status = 'Not Started';
        } elseif ($progress < $closed_progress) {
            $status = 'In Progress';
            if ($budget_cost) {
                $to_date_cost = ActualResources::whereIn('breakdown_resource_id', $resources->pluck('id'))->sum('cost');
                $progress = $to_date_cost * 100 / $budget_cost;
                if ($progress > 100) {
                    $progress = 99;
                }
            } else {
                $progress = 99;
            }
        } else {
            $progress = 100;
            $status = 'Closed';
        }

        $newResource = BreakdownResource::forceCreate([
            'breakdown_id' => 0, 'wbs_id' => $firstShadow->wbs_id, 'project_id' => $firstShadow->project_id,
            'resource_id' => $resource_id, 'budget_qty' => $budget_qty, 'eng_qty' => $eng_qty,
            'important' => $important, 'labor_count' => $labor_count, 'remarks' => $remarks,
            'resource_waste' => $firstShadow->resource_waste, 'equation' => $budget_unit,
            'productivity_id' => 0, 'code' => $firstShadow->code,
            'created_at' => $this->now, 'updated_at' => $this->now,
            'created_by' => $this->user_id, 'updated_by' => $this->user_id
        ]);

        $newShadow = BreakDownResourceShadow::forceCreate([
            'breakdown_id' => 0, 'wbs_id' => $firstShadow->wbs_id, 'project_id' => $firstShadow->project_id,
            'breakdown_resource_id' => $newResource->id, 'activity_id' => $firstShadow->activity_id,
            'resource_type_id' => $firstShadow->resource_type_id, 'cost_account' => '',
            'template' => '', 'activity' => $firstShadow->activity,
            'eng_qty' => $eng_qty, 'budget_qty' => $budget_qty, 'resource_qty' => $budget_unit,
            'resource_waste' => $firstShadow->resource_waste, 'resource_type' => $firstShadow->resource_type, 'resource_code' => $firstShadow->resource_code,
            'resource_name' => $firstShadow->resource_name, 'unit_price' => $firstShadow->unit_price,
            'measure_unit' => $firstShadow->measure_unit, 'budget_unit' => $budget_unit, 'budget_cost' => $budget_cost,
            'boq_equivilant_rate' => $shadows->sum('boq_equivilant_rate'),
            'labors_count' => $labor_count, 'productivity_output' => $shadows->sum('productivity_output'), 'productivity_ref' => '',
            'remarks' => $remarks, 'resource_id' => $resource_id, 'productivity_id' => 0,
            'unit_id' => $firstShadow->unit_id, 'template_id' => $firstShadow->template_id,
            'code' => $firstShadow->code,
            'progress' => $progress, 'status' => $status,
            'boq_id' => $firstShadow->boq_id, 'survey_id' => $firstShadow->survey_id, 'boq_wbs_id' => $firstShadow->boq_wbs_id,
            'survey_wbs_id' => $firstShadow->survey_wbs_id, 'boq_qs_id' => $firstShadow->boq_qs_id,
            'important' => $important, 'show_in_budget' => 0, 'show_in_cost' => 1, 'is_rollup' => 0,
            'created_at' => $this->now, 'updated_at' => $this->now, 'created_by' => $this->user_id, 'updated_by' => $this->user_id,
            'is_sum' => 1
        ]);

        BreakDownResourceShadow::whereIn('id', $shadows->pluck('id'))->update([
            'show_in_budget' => 1, 'show_in_cost' => 0, 'summed_at' => $this->now
        ]);

        $period = $this->wbs->project->open_period();
        if ($period) {
            $query = ActualResources::where('period_id', $period->id)->whereIn('breakdown_resource_id', $resources->pluck('id'));
            $actuals = $query->get();

            $cost = $actuals->sum('cost');
            $qty = $actuals->sum('qty');

            if ($qty) {
                $unit_price = $cost / $qty;
                ActualResources::forceCreate([
                    'period_id' => $period->id, 'breakdown_resource_id' => $newResource->id,
                    'wbs_level_id' => $newResource->wbs_id, 'project_id' => $newResource->project_id,
                    'cost' => $cost, 'qty' => $qty, 'unit_price' => $unit_price,
                    'unit_id' => $newShadow->unit_id, 'action_date' => $this->now,
                    'created_at' => $this->now, 'updated_at' => $this->now,
                    'user_id' => $this->user_id, 'created_by' => $this->user_id, 'updated_by' => $this->user_id,
                    'progress' => $progress, 'status' => $status, 'doc_no' => '',
                    'batch_id' => 0,
                ]);

                $query->delete();
            }

        }
    }
}