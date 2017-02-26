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
    //todo to be complete after modifications ...
    protected $project;
    protected $activities;
    protected $period_id;
    protected $levels;


    function getActivityReport(Project $project, $period_id)
    {
        $this->project = $project;
        $this->period_id = $period_id;
        $this->levels = $project->wbs_levels()->get()->keyBy('id')->map(function ($level){
           return $level;
        });
        $tree = [];

        $wbs_tree = \Cache::get('wbs-tree-' . $project->id) ?: $project->wbs_tree;

        foreach ($wbs_tree as $level) {
            $treeLevel = $this->buildTree($level);
            $tree[] = $treeLevel;

        }
        return view('reports.cost-control.activity.activity_report', compact('tree', 'project'));
    }


    /**
     * @param $level
     * @return array
     */
    function buildTree($level)
    {
        $actual_level = $this->levels->get($level['id']);

        $tree = ['id' => $level['id'], 'name' => $level['name'], 'children' => [], 'activities' => [], 'data' => [
            'budget_cost' => 0,
            'prev_cost' => 0,
            'prev_allowable' => 0,
            'prev_variance' => 0,
            'to_date_cost' => 0,
            'allowable_cost' => 0,
            'cost_var' => 0,
            'remain_cost' => 0,
            'allowable_var' => 0,
            'completion_cost' => 0,
        ]];

        $period_activities = \DB::select('SELECT
  sh.activity_id,sh.activity,
  SUM(budget_cost) AS budget_cost,
  SUM(cost.to_date_cost) AS to_date_cost,
  SUM(cost.allowable_ev_cost) AS allowable_cost,
  SUM(cost.cost_var) AS cost_var,
  SUM(cost.remaining_cost) AS remain_cost,
  SUM(cost.allowable_var) AS allowable_var,
  SUM(cost.completion_cost) AS completion_cost
FROM break_down_resource_shadows sh JOIN
  cost_shadows cost ON sh.breakdown_resource_id = cost.breakdown_resource_id
WHERE sh.project_id = ? AND cost.period_id =? AND sh.wbs_id=?
GROUP BY activity_id , sh.activity', [$this->project->id, $this->period_id,$level['id']]);
        $prev_activities = \DB::select('SELECT
  sh.activity_id,sh.activity,
  SUM(budget_cost) AS budget_cost,
  SUM(cost.to_date_cost) AS to_date_cost,
  SUM(cost.allowable_ev_cost) AS allowable_cost,
  SUM(cost.cost_var) AS cost_var,
  SUM(cost.remaining_cost) AS remain_cost,
  SUM(cost.allowable_var) AS allowable_var,
  SUM(cost.completion_cost) AS completion_cost
FROM break_down_resource_shadows sh JOIN
  cost_shadows cost ON sh.breakdown_resource_id = cost.breakdown_resource_id
WHERE sh.project_id = ? AND cost.period_id < ? AND sh.wbs_id=?
GROUP BY activity_id , sh.activity', [$this->project->id, $this->period_id,$level['id']]);

        if (count($period_activities)) {
            foreach ($period_activities as $activity) {
                if(!isset($tree['activities'][$activity->activity_id])){
                    $tree['activities'][$activity->activity_id]=[
                        'activity_name'=>$activity->activity,
                        'budget_cost'=>0,
                        'prev_cost'=>0,
                        'prev_allowable'=>0,
                        'prev_variance'=>0,
                        'to_date_cost'=>0,
                        'allowable_cost'=>0,
                        'cost_var'=>0,
                        'remain_cost'=>0,
                        'allowable_var'=>0,
                        'completion_cost'=>0,
                    ];
                }
                $tree['activities'][$activity->activity_id]['budget_cost'] += $activity->budget_cost;
                $tree['activities'][$activity->activity_id]['to_date_cost'] += $activity->to_date_cost;
                $tree['activities'][$activity->activity_id]['allowable_cost'] += $activity->allowable_cost;
                $tree['activities'][$activity->activity_id]['cost_var'] += $activity->cost_var;
                $tree['activities'][$activity->activity_id]['remain_cost'] += $activity->remain_cost;
                $tree['activities'][$activity->activity_id]['allowable_var'] += $activity->allowable_var;
                $tree['activities'][$activity->activity_id]['completion_cost'] += $activity->completion_cost;
                $tree['data']['budget_cost']+=$activity->budget_cost;
                $tree['data']['to_date_cost']+=$activity->to_date_cost;
                $tree['data']['allowable_cost']+=$activity->allowable_cost;
                $tree['data']['cost_var']+=$activity->cost_var;
                $tree['data']['remain_cost']+=$activity->remain_cost;
                $tree['data']['allowable_var']+=$activity->allowable_var;
                $tree['data']['completion_cost']+=$activity->completion_cost;

            }
        }
        if(count($prev_activities)){
            foreach ($period_activities as $prev_activity) {
                $tree['activities'][$prev_activity->activity_id]['prev_cost'] += $prev_activity->to_date_cost;
                $tree['activities'][$prev_activity->activity_id]['prev_allowable'] += $prev_activity->allowable_cost;
                $tree['activities'][$prev_activity->activity_id]['prev_variance'] += $prev_activity->cost_var;
                $tree['data']['prev_cost']+=$prev_activity->to_date_cost;
                $tree['data']['prev_allowable']+=$prev_activity->prev_allowable;
                $tree['data']['prev_variance']+=$prev_activity->prev_variance;

            }
        }

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
        }

        return $tree;
    }


}