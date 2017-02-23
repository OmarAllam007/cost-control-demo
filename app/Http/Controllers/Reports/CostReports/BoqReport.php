<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 24/12/16
 * Time: 07:39 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\ActivityDivision;
use App\Breakdown;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\StdActivity;
use App\WbsLevel;

class BoqReport
{
    protected $root_ids = [];
    protected $divisions;
    protected $project;
    protected $activities;
    protected $div_activities;
    protected $prev_activities;

    function getReport(Project $project, $period_id)
    {
        $this->project = $project;
        $this->divisions = collect();
        $this->activities = collect();
        $this->div_activities = collect();
        $this->prev_activities = collect();
        $tree = [];

        $wbs_levels = \Cache::get('wbs-tree-' . $project->id) ?: $project->wbs_tree;
        $this->divisions = ActivityDivision::all()->keyBy('id')->map(function ($division) {
            return $division;
        });

        collect(\DB::select('SELECT activity,
  sh.activity_id,
  sh.wbs_id,
  SUM(sh.budget_cost) AS budget_cost,
  SUM(cost.to_date_cost) AS to_date_cost,
  SUM(cost.allowable_ev_cost) AS allowable_cost,
  SUM(allowable_var) AS allowable_var,
  SUM(remaining_cost) AS remain_cost,
  SUM(completion_cost) AS completion_cost
FROM break_down_resource_shadows sh JOIN cost_shadows cost
WHERE sh.breakdown_resource_id = cost.breakdown_resource_id AND sh.project_id = ? AND cost.period_id=?
GROUP BY activity_id , sh.wbs_id', [$project->id, $period_id]))->map(function ($activity) {
            $this->activities->put($activity->activity_id . $activity->wbs_id, ['name'=>$activity->activity,'budget_cost' => $activity->budget_cost,
                'to_date_cost' => $activity->to_date_cost, 'allowable_cost' => $activity->allowable_cost, 'allowable_var' => $activity->allowable_var
                ,'remain_cost'=>$activity->remain_cost , 'completion_cost'=>$activity->completion_cost
            ]);
        });

        collect(\DB::select('SELECT activity,
  sh.activity_id,
  sh.wbs_id,
  SUM(cost.to_date_cost) AS to_date_cost,
  SUM(cost.allowable_ev_cost) AS allowable_cost,
  SUM(allowable_var) AS allowable_var
FROM break_down_resource_shadows sh JOIN cost_shadows cost
WHERE sh.breakdown_resource_id = cost.breakdown_resource_id AND sh.project_id = ? AND cost.period_id < ?
GROUP BY activity_id , sh.wbs_id', [$project->id, $period_id]))->map(function ($activity) {
            $this->prev_activities->put($activity->activity_id . $activity->wbs_id, ['name'=>$activity->activity,
                'to_date_cost' => $activity->to_date_cost, 'allowable_cost' => $activity->allowable_cost, 'allowable_var' => $activity->allowable_var
            ]);
        });

        $this->div_activities = StdActivity::all()->keyBy('id')->map(function ($activity) {
            return ActivityDivision::find($activity->division_id);
        });
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildTree($level);
            $tree[] = $treeLevel;
        }
        return view('reports.cost-control.boq-report.boq_report', compact('tree', 'levels', 'project'));
    }

    function buildTree($level)
    {

        $tree = ['id' => $level['id'], 'name' => $level['name'], 'children' => [], 'division' => [], 'data' => []];

        $activities_id = BreakDownResourceShadow::where('project_id', $this->project->id)->where('wbs_id', $level['id'])
            ->get()->pluck('activity_id')->unique()->toArray();
        $activities = StdActivity::whereIn('id', $activities_id)->get();

        foreach ($activities as $activity) {
            $division = $this->div_activities->get($activity->id);
            if (!isset($tree['division'][$division->id])) {
                $tree['division'][$division->id] = [
                    'name' => $division->name,
                    'activities' => [],
                ];
            }

            if (!isset($tree['division'][$division->id]['activities'][$activity->id])) {
                $tree['division'][$division->id]['activities'][$activity->id] = [
                    'name' => $this->activities->get($activity->id . $level['id'])['name'],
                    'budget_cost' => $this->activities->get($activity->id . $level['id'])['budget_cost'],
                    'prev_cost' => $this->prev_activities->get($activity->id . $level['id'])['to_date_cost'],
                    'prev_allowable' => $this->prev_activities->get($activity->id . $level['id'])['allowable_cost'],
                    'prev_var' => $this->prev_activities->get($activity->id . $level['id'])['allowable_var'],
                    'to_date_cost' => $this->activities->get($activity->id . $level['id'])['to_date_cost'],
                    'allowable_cost' => $this->activities->get($activity->id . $level['id'])['allowable_cost'],
                    'allowable_var' => $this->activities->get($activity->id . $level['id'])['allowable_var'],
                    'remain_cost' => $this->activities->get($activity->id . $level['id'])['remain_cost'],
                    'completion_cost' => $this->activities->get($activity->id . $level['id'])['completion_cost'],
                ];
            }

        }


        if (count($level['children'])) {
            $tree['children'] = collect($level['children'])->map(function ($childLevel) {
                return $this->buildTree($childLevel);
            });
        }

        return $tree;
    }


}