<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 28/12/16
 * Time: 02:07 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\Breakdown;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\WbsLevel;

class ActivityReport
{

    protected $project;
    protected $activities;
    protected $period_id;
    protected $levels;
    protected $cost_data;
    protected $prev_data;
    protected $budget_data;
    protected $budget_activites;
    protected $total;


    function getActivityReport(Project $project, $period_id)
    {
        $this->project = $project;
        $this->period_id = $period_id;
        $this->cost_data = collect();
        $this->budget_data= collect();
        $this->prev_data= collect();
        $this->activites = [];
        $this->total = ['budget_cost' => 0,
            'to_data_cost' => 0,
            'to_date_allowable_cost' => 0,
            'cost_var' => 0,
            'remain_cost' => 0,
            'allowable_var' => 0,
            'completion_cost' => 0,
            'prev_cost' => 0,
            'prev_allowable' => 0,
            'prev_variance' => 0,];

        $tree = [];
        collect(\DB::select('SELECT
  activity_id,
  wbs_id,
  activity,
  sum(allowable_ev)     allowable_cost,
  sum(to_date_cost)     to_date_cost,
  sum(to_date_variance) to_date_var,
  sum(remaining_cost)   remain_cost,
  sum(completion_cost)  comp_cost,
  sum(cost_var) cost_var
FROM (SELECT
        budget.activity_id     AS activity_id,
        budget.activity        AS activity,
        budget.wbs_id          AS wbs_id,
        sum(allowable_ev_cost) AS allowable_ev,
        sum(to_date_cost)      AS to_date_cost,
        sum(allowable_var)     AS to_date_variance,
        sum(remaining_cost)    AS remaining_cost,
        sum(completion_cost)   AS completion_cost,
        sum(cost_var) AS cost_var
      FROM cost_shadows AS cost
        LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
      WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                       FROM cost_shadows p
                                                       WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND
                                                             cost.period_id <= ?)
      GROUP BY 1, 2, 3) AS data
GROUP BY 1, 2, 3;', [$project->id, $period_id]))->map(function ($activity) {
            $this->cost_data->put($activity->wbs_id . $activity->activity_id, [
                'to_date_cost' => $activity->to_date_cost,
                'allowable_cost' => $activity->allowable_cost,
                'allowable_var' => $activity->to_date_var,
                'completion_cost' => $activity->comp_cost,
                'cost_var' => $activity->cost_var,
                'remain_cost' => $activity->remain_cost,

            ]);
        });

        collect(\DB::select('SELECT
  activity_id,
  wbs_id,
  activity,
  sum(allowable_ev)     allowable_cost,
  sum(to_date_cost)     to_date_cost,
  sum(to_date_variance) to_date_var,
  sum(remaining_cost)   remain_cost,
  sum(completion_cost)  comp_cost,
  sum(cost_var) cost_var
FROM (SELECT
        budget.activity_id     AS activity_id,
        budget.activity        AS activity,
        budget.wbs_id          AS wbs_id,
        sum(allowable_ev_cost) AS allowable_ev,
        sum(to_date_cost)      AS to_date_cost,
        sum(allowable_var)     AS to_date_variance,
        sum(remaining_cost)    AS remaining_cost,
        sum(completion_cost)   AS completion_cost,
        sum(cost_var) AS cost_var
      FROM cost_shadows AS cost
        LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
      WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                       FROM cost_shadows p
                                                       WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND
                                                             cost.period_id < ?)
      GROUP BY 1, 2, 3) AS data
GROUP BY 1, 2, 3;', [$project->id, $period_id]))->map(function ($activity) {
            $this->prev_data->put($activity->wbs_id . $activity->activity_id, [
                'to_date_cost' => $activity->to_date_cost,
                'allowable_cost' => $activity->allowable_cost,
                'allowable_var' => $activity->to_date_var,
            ]);
        });
        collect(\DB::select('SELECT
  sh.activity_id,
  sh.activity,
  sh.wbs_id,
  SUM(sh.budget_cost) AS budget_cost
FROM break_down_resource_shadows sh 
WHERE sh.project_id = ? 
GROUP BY activity_id , sh.activity ,sh.wbs_id', [$project->id]))->map(function ($activity) {
            $this->budget_data->put($activity->wbs_id . $activity->activity_id, $activity->budget_cost);
        });
            collect(\DB::select('SELECT activity_id ,activity, wbs_id FROM break_down_resource_shadows WHERE project_id=?', [$project->id]))->map(function ($activity) {
            if (!isset($this->budget_activites[$activity->wbs_id]['activities'][$activity->activity_id])) {
                $this->budget_activites[$activity->wbs_id]['activities'][$activity->activity_id] = ['name' => $activity->activity, 'id' => $activity->activity_id];
            }
        });


        $wbs_tree = \Cache::get('wbs-tree-' . $project->id) ?: $project->wbs_tree;

        foreach ($wbs_tree as $level) {
            $treeLevel = $this->buildTree($level);
            $tree[] = $treeLevel;

        }
        $total = $this->total;
        return view('reports.cost-control.activity.activity_report', compact('tree', 'project','total'));
    }

    function buildTree($level)
    {

        $tree = ['id' => $level['id'], 'name' => $level['name'], 'children' => [], 'activities' => [], 'data' => [
            'budget_cost' => 0,
            'prev_cost' => 0,
            'prev_allowable' => 0,
            'prev_var' => 0,
            'to_date_cost' => 0,
            'allowable_cost' => 0,
            'cost_var' => 0,
            'remain_cost' => 0,
            'allowable_var' => 0,
            'completion_cost' => 0,

        ]];

        $activities = collect($this->budget_activites)->get($level['id']);

        if (count($activities)) {
            foreach ($activities['activities'] as $activity) {
                if (!isset($tree['activities'][$activity['id']])) {
                    $tree['activities'][$activity['id']] = [
                        'activity_name' => $activity['name'],
                        'budget_cost' => 0,
                        'prev_cost' => 0,
                        'prev_allowable' => 0,
                        'prev_var' => 0,
                        'to_date_cost' => 0,
                        'allowable_cost' => 0,
                        'cost_var' => 0,
                        'remain_cost' => 0,
                        'allowable_var' => 0,
                        'completion_cost' => 0,
                    ];
                }
                $tree['activities'][$activity['id']]['budget_cost'] += $this->budget_data->get($level['id'] . $activity['id']);
                $tree['activities'][$activity['id']]['prev_cost'] += $this->prev_data->get($level['id'] . $activity['id'])['to_date_cost'] ?? 0;
                $tree['activities'][$activity['id']]['prev_allowable'] += $this->prev_data->get($level['id'] . $activity['id'])['allowable_cost'] ?? 0 ;
                $tree['activities'][$activity['id']]['prev_var'] += $this->prev_data->get($level['id'] . $activity['id'])['allowable_var'] ?? 0;
                $tree['activities'][$activity['id']]['to_date_cost'] += $this->cost_data->get($level['id'] . $activity['id'])['to_date_cost'];
                $tree['activities'][$activity['id']]['allowable_cost'] += $this->cost_data->get($level['id'] . $activity['id'])['allowable_cost'];
                $tree['activities'][$activity['id']]['cost_var'] += $this->cost_data->get($level['id'] . $activity['id'])['cost_var'];
                $tree['activities'][$activity['id']]['remain_cost'] += $this->cost_data->get($level['id'] . $activity['id'])['remain_cost'];
                $tree['activities'][$activity['id']]['allowable_var'] += $this->cost_data->get($level['id'] . $activity['id'])['allowable_var'];
                $tree['activities'][$activity['id']]['completion_cost'] += $this->cost_data->get($level['id'] . $activity['id'])['completion_cost'];

                $tree['data']['budget_cost'] += $this->budget_data->get($level['id'] . $activity['id']);
                $tree['data']['to_date_cost'] += $this->cost_data->get($level['id'] . $activity['id'])['to_date_cost'];
                $tree['data']['allowable_cost'] += $this->cost_data->get($level['id'] . $activity['id'])['allowable_cost'];
                $tree['data']['cost_var'] += $this->cost_data->get($level['id'] . $activity['id'])['cost_var'];
                $tree['data']['remain_cost'] += $this->cost_data->get($level['id'] . $activity['id'])['remain_cost'];
                $tree['data']['allowable_var'] += $this->cost_data->get($level['id'] . $activity['id'])['allowable_var'];
                $tree['data']['completion_cost'] += $this->cost_data->get($level['id'] . $activity['id'])['completion_cost'];
                $tree['data']['prev_cost'] += $this->prev_data->get($level['id'] . $activity['id'])['to_date_cost'];
                $tree['data']['prev_allowable'] += $this->prev_data->get($level['id'] . $activity['id'])['allowable_cost'];
                $tree['data']['prev_var'] += $this->prev_data->get($level['id'] . $activity['id'])['allowable_var'];

            }
        }
        $this->total['budget_cost']+=$tree['data']['budget_cost'];
        $this->total['to_date_allowable_cost']+=$tree['data']['allowable_cost'];
        $this->total['to_data_cost']+=$tree['data']['to_date_cost'];
        $this->total['allowable_var']+=$tree['data']['allowable_var'];
        $this->total['remain_cost']+=$tree['data']['remain_cost'];
        $this->total['cost_var']+=$tree['data']['cost_var'];
        $this->total['completion_cost']+=$tree['data']['completion_cost'];
        $this->total['prev_cost']+=$tree['data']['prev_cost'];
        $this->total['prev_allowable']+=$tree['data']['prev_allowable'];
        $this->total['prev_variance']+=$tree['data']['prev_var'];

        if (collect($level['children'])->count()) {
            $tree['children'] = collect($level['children'])->map(function ($childLevel) {
                return $this->buildTree($childLevel);
            });
        }
        foreach ($tree['children'] as $child) {
            $tree['data']['budget_cost'] += $child['data']['budget_cost'];
            $tree['data']['to_date_cost'] += $child['data']['to_date_cost'];
            $tree['data']['allowable_cost'] += $child['data']['allowable_cost'];
            $tree['data']['cost_var'] += $child['data']['cost_var'];
            $tree['data']['remain_cost'] += $child['data']['remain_cost'];
            $tree['data']['allowable_var'] += $child['data']['allowable_var'];
            $tree['data']['completion_cost'] += $child['data']['completion_cost'];
            $tree['data']['prev_cost'] += $child['data']['prev_cost'];
            $tree['data']['prev_allowable'] += $child['data']['prev_allowable'];
            $tree['data']['prev_var'] += $child['data']['prev_var'];
        }

        return $tree;
    }


}