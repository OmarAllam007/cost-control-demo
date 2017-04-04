<?php

namespace App\Http\Controllers\Reports\CostReports;

use App\ActivityDivision;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\StdActivity;
use Illuminate\Support\Collection;

class CostStandardActivityReport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    /** @var Period */
    protected $previousPeriod;

    /** @var Collection */
    protected $activityNames;

    function getStandardActivities(Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->period = $project->periods()->find($chosen_period_id);
        $this->previousPeriod = $project->periods()->where('id', '<', $chosen_period_id)->orderBy('id')->first();
        if ($this->previousPeriod) {
            $previousTotals = MasterShadow::whereProjectId($project->id)->wherePeriodId($this->previousPeriod->id)
                ->selectRaw('sum(to_date_cost) previous_cost, sum(allowable_ev_cost) previous_allowable, sum(allowable_var) as previous_var')
                ->first();
        } else {
            $previousTotals = ['previous_cost' => 0, 'previous_allowable' => 0, 'previous_var' => 0];
        }

        $currentTotals = MasterShadow::whereProjectId($project->id)->wherePeriodId($this->period->id)->selectRaw(
            'sum(to_date_cost) to_date_cost, sum(allowable_ev_cost) to_date_allowable, sum(allowable_var) as to_date_var,'
            . 'sum(remaining_cost) as remaining, sum(completion_cost) at_completion_cost, sum(cost_var) cost_var, sum(budget_cost) budget_cost'
        )->first();

        $tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->pluck('name', 'id');
        $activityNames = $this->activityNames;
        $divisionNames = ActivityDivision::parents()->orderBy('code')->orderBy('name')->get(['id', 'code', 'name'])
            ->keyBy('id')->map(function (ActivityDivision $div) {
                return $div->code . ' ' . $div->name;
            });

        return view('reports.cost-control.standard_activity.standard_activity',
            compact('project', 'period', 'currentTotals', 'previousTotals', 'tree', 'periods', 'activityNames', 'divisionNames'));
    }

    protected function buildTree()
    {
        $query = \DB::table('master_shadows')->whereProjectId($this->project->id)->wherePeriodId($this->period->id)->selectRaw(
            'activity_id, activity, sum(to_date_cost) to_date_cost, sum(allowable_ev_cost) to_date_allowable, sum(allowable_var) as to_date_var,'
            . 'sum(remaining_cost) as remaining_cost, sum(completion_cost) completion_cost, sum(cost_var) completion_var, sum(budget_cost) budget_cost'
        );

        $this->applyFilters($query);

        $currentActivities = collect($query->groupBy('activity', 'activity_id')->orderBy('activity')->get())->keyBy('activity_id');
        $activity_ids = $currentActivities->pluck('activity_id');
        $this->activityNames = $currentActivities->pluck('activity', 'activity_id')->sort();

        if ($this->previousPeriod) {
            $previousActivities = collect(\DB::table('master_shadows')->whereProjectId($this->project->id)->wherePeriodId($this->previousPeriod->id)->selectRaw(
                'activity_id, activity, sum(to_date_cost) previous_cost, sum(allowable_ev_cost) previous_allowable, sum(allowable_var) as previous_var'
            )->whereIn('activity_id', $activity_ids)->groupBy('activity', 'activity_id')->orderBy('activity')->get())->keyBy('activity_id');
        } else {
            $previousActivities = [];
        }

        $activityDivs = collect(\DB::table('master_shadows')->whereProjectId($this->project->id)
            ->wherePeriodId($this->period->id)->whereIn('activity_id', $activity_ids)
            ->pluck('activity_divs', 'activity_id'))->map(function ($div) {
            return json_decode($div, true);
        });

        $tree = [];

        foreach ($currentActivities as $id => $current) {
            $prevDiv = '';
            $previous = $previousActivities[$id] ?? [];
            foreach ($activityDivs[$id] as $index => $div) {
                if (!isset($tree[$div])) {
                    $tree[$div] = [
                        'budget_cost' => 0, 'to_date_cost' => 0, 'to_date_allowable' => 0, 'to_date_var' => 0,
                        'previous_cost' => 0, 'previous_allowable' => 0, 'previous_var' => 0,
                        'remaining_cost' => 0, 'completion_cost' => 0, 'completion_var' => 0
                    ];
                }

                $tree[$div]['index'] = $index;
                $tree[$div]['parent'] = $prevDiv;
                $tree[$div]['budget_cost'] += $current->budget_cost;
                $tree[$div]['to_date_cost'] += $current->to_date_cost;
                $tree[$div]['to_date_allowable'] += $current->to_date_allowable;
                $tree[$div]['to_date_var'] += $current->to_date_var;
                $tree[$div]['remaining_cost'] += $current->remaining_cost;
                $tree[$div]['completion_cost'] += $current->completion_cost;
                $tree[$div]['completion_var'] += $current->completion_var;
                $tree[$div]['previous_cost'] += $previous->previous_cost ?? 0;
                $tree[$div]['previous_allowable'] += $previous->previous_allowable ?? 0;
                $tree[$div]['previous_var'] += $previous->previous_var ?? 0;

                $prevDiv = $div;
            }

            $tree[$prevDiv]['activities'][] = [
                'name' => $current->activity,
                'budget_cost' => $current->budget_cost,
                'to_date_cost' => $current->to_date_cost,
                'to_date_allowable' => $current->to_date_allowable,
                'to_date_var' => $current->to_date_var,
                'remaining_cost' => $current->remaining_cost,
                'completion_cost' => $current->completion_cost,
                'completion_var' => $current->completion_var,
                'previous_cost' => $previous->previous_cost ?? 0,
                'previous_allowable' => $previous->previous_allowable ?? 0,
                'previous_var' => $previous->previous_var ?? 0,
            ];

        }

        return collect($tree)->sortByKeys();
    }

    protected function applyFilters($query)
    {
        $request = request();

        if ($activity_id = $request->get('activity')) {
            $query->where('activity_id', $activity_id);
        }

        if ($status = strtolower($request->get('status', ''))) {
            if ($status == 'not started') {
                $query->havingRaw('sum(to_date_qty) = 0');
            } elseif ($status == 'in progress') {
                $query->havingRaw('sum(to_date_qty) > 0 AND AVG(progress) < 100');
            } elseif ($status == 'closed') {
                $query->where('to_date_qty', '>', 0)->where('progress', 100);
            }
        }

        if ($div_id = $request->get('div')) {
            $div = ActivityDivision::find($div_id);
            if ($div) {
                $activity_ids = StdActivity::whereIn('division_id', $div->getChildrenIds())->pluck('id');
                $query->whereIn('activity_id', $activity_ids);
            }
        }

        if ($request->exists('negative')) {
            $query->having('to_date_var', '<', 0);
        }

    }
}