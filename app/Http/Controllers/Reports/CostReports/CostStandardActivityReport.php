<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 22/12/16
 * Time: 02:17 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\ActivityDivision;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Http\Controllers\CostConcernsController;
use App\Http\Controllers\Reports\Productivity;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\ResourceType;
use App\StdActivity;
use App\WbsLevel;

class CostStandardActivityReport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    /** @var Period */
    protected $previousPeriod;

    function getStandardActivities(Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->period = $project->periods()->find($chosen_period_id);
        $this->previousPeriod = $project->periods()->where('id', '<', $chosen_period_id)->orderBy('id')->first();

        $previousTotals = MasterShadow::whereProjectId($project->id)->wherePeriodId($this->previousPeriod->id)
            ->selectRaw('sum(to_date_cost) previous_cost, sum(allowable_ev_cost) previous_allowable, sum(allowable_var) as previous_var')
            ->first();

        $currentTotals = MasterShadow::whereProjectId($project->id)->wherePeriodId($this->period->id)->selectRaw(
            'sum(to_date_cost) to_date_cost, sum(allowable_ev_cost) to_date_allowable, sum(allowable_var) as to_date_var,'
            . 'sum(remaining_cost) as remaining, sum(completion_cost) at_completion_cost, sum(cost_var) cost_var, sum(budget_cost) budget_cost'
        )->first();


        $tree = $this->buildTree();

        return view('reports.cost-control.standard_activity.standard_activity', compact('project', 'period', 'currentTotals', 'previousTotals', 'tree'));
    }

    protected function buildTree()
    {
        $currentActivities = collect(\Db::table('master_shadows')->whereProjectId($this->project->id)->wherePeriodId($this->period->id)->selectRaw(
            'activity_id, activity, sum(to_date_cost) to_date_cost, sum(allowable_ev_cost) to_date_allowable, sum(allowable_var) as to_date_var,'
            . 'sum(remaining_cost) as remaining_cost, sum(completion_cost) completion_cost, sum(cost_var) completion_var, sum(budget_cost) budget_cost'
        )->groupBy('activity', 'activity_id')->orderBy('activity')->get())->keyBy('activity_id');

        $previousActivities = collect(\Db::table('master_shadows')->whereProjectId($this->project->id)->wherePeriodId($this->previousPeriod->id)->selectRaw(
            'activity_id, activity, sum(to_date_cost) previous_cost, sum(allowable_ev_cost) previous_allowable, sum(allowable_var) as previous_var'
        )->groupBy('activity', 'activity_id')->orderBy('activity')->get())->keyBy('activity_id');

        $activityDivs = collect(\DB::table('master_shadows')->whereProjectId($this->project->id)
            ->wherePeriodId($this->period->id)->pluck('activity_divs', 'activity_id'))->map(function($div) {
                return json_decode($div, true);
        });


        $tree = [];

        foreach ($currentActivities as $id => $current) {
            $prevDiv = '';
            $previous = $previousActivities[$id];
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
                $tree[$div]['previous_cost'] += $previous->previous_cost;
                $tree[$div]['previous_allowable'] += $previous->previous_allowable;
                $tree[$div]['previous_var'] += $previous->previous_var;

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
                'previous_cost' => $previous->previous_cost,
                'previous_allowable' => $previous->previous_allowable,
                'previous_var' => $previous->previous_var,
            ];

        }

        return collect($tree)->sortByKeys();
    }
}