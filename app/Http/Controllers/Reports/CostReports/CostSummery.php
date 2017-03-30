<?php

namespace App\Http\Controllers\Reports\CostReports;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostConcerns;
use App\CostShadow;
use App\Http\Controllers\CostConcernsController;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\ResourceType;

class CostSummery
{

    function getCostSummery (Project $project, $chosen_period_id)
    {
        $period_id = request('period_id', $chosen_period_id);
        $resourceTypes = ResourceType::where('parent_id', 0)->whereIn('id', [20, 21, 40, 96, 1, 17, 81, 95])
            ->orderBy('name')->pluck('name', 'id');

        $previousPeriod = $project->periods()->where('id', '<', $period_id)->first();
        if ($previousPeriod) {
            $previousData = MasterShadow::where('period_id', '=', $previousPeriod->id)
                ->selectRaw('resource_type_id, sum(to_date_cost) as previous_cost, sum(allowable_ev_cost) as previous_allowable, sum(allowable_var) as previous_var')
                ->groupBy('resource_type_id')->get()->keyBy('resource_type_id');
        } else {
            $previousData = collect();
        }

        $fields = [
            'resource_type_id', 'sum(budget_cost) budget_cost', 'sum(to_date_cost) as to_date_cost', 'sum(allowable_ev_cost) as to_date_allowable',
            'sum(allowable_var) as to_date_var', 'sum(remaining_cost) as remaining_cost', 'sum(completion_cost) as completion_cost',
            'sum(cost_var) as completion_cost_var'
        ];

        $toDateData = MasterShadow::where('period_id', $period_id)->selectRaw(implode(', ', $fields))->groupBy('resource_type_id')->get()->keyBy('resource_type_id');
        return view('reports.cost-control.cost_summery', compact('previousData', 'toDateData', 'project', 'resourceTypes'));
    }
}