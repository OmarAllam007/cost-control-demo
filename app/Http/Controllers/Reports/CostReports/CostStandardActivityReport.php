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
use App\Project;
use App\ResourceType;
use App\StdActivity;
use App\WbsLevel;

class CostStandardActivityReport
{
    private $project;
    private $project_activities;
    private $prev_shadow;
    private $period_shadow;
    private $resourcesActivity;
    private $budgetData;
    private $total;

    function getStandardActivities (Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->prev_shadow = collect();
        $this->period_shadow = collect();
        $this->project_activities = collect();
        $this->budgetData = collect();
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

        //previous data
        collect(\DB::select('SELECT
  resource_id,
  sum(allowable_ev) AS  allowable_cost,
  sum(to_date_cost)     to_date_cost,
  sum(to_date_variance) to_date_var
FROM (SELECT
        resource_id,
        sum(allowable_ev_cost) AS allowable_ev,
        sum(to_date_cost)      AS to_date_cost,
        sum(allowable_var)     AS to_date_variance
      FROM cost_shadows AS cost
      WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                       FROM cost_shadows p
                                                       WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND
                                                             p.period_id < ?)
      GROUP BY 1) AS DATA
GROUP BY 1;', [$this->project->id, $chosen_period_id]))->map(function ($resource) {
            $this->prev_shadow->put($resource->resource_id, ['prev_cost' => $resource->to_date_cost
                , 'prev_allowable' => $resource->allowable_cost
                , 'prev_variance' => $resource->to_date_var
            ]);
        });


        //current data
        collect(\DB::select('SELECT
  resource_id,
  sum(allowable_ev) AS  allowable_cost,
  sum(to_date_cost)     to_date_cost,
  sum(to_date_variance) to_date_var,
  sum(remaining_cost)   remain_cost,
  sum(completion_cost)  comp_cost,
  sum(cost_var)         cost_var
FROM (SELECT
        resource_id,
        sum(allowable_ev_cost) AS allowable_ev,
        sum(to_date_cost)      AS to_date_cost,
        sum(allowable_var)     AS to_date_variance,
        sum(remaining_cost)    AS remaining_cost,
        sum(completion_cost)   AS completion_cost,
        sum(cost_var)          AS cost_var

      FROM cost_shadows AS cost
      WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                       FROM cost_shadows p
                                                       WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND
                                                             p.period_id <= ?)
      GROUP BY 1) AS DATA
GROUP BY 1;', [$this->project->id, $chosen_period_id]))->map(function ($resource) {
            $this->period_shadow->put($resource->resource_id,
                [
                    'to_data_cost' => $resource->to_date_cost
                    , 'to_date_allowable_cost' => $resource->allowable_cost
                    , 'cost_var' => $resource->cost_var
                    , 'remain_cost' => $resource->remain_cost
                    , 'allowable_var' => $resource->to_date_var
                    , 'completion_cost' => $resource->comp_cost
                ]);
        });


        collect(\DB::select('SELECT sh.activity_id , sum(sh.budget_cost) AS budget_cost FROM break_down_resource_shadows sh
WHERE project_id=? GROUP BY activity_id', [$project->id]))->map(function ($activity) {
            $this->project_activities->put($activity->activity_id, $activity->budget_cost);
        });

        //current data for resources to calculate activity data based on activity division
        collect(\DB::select('SELECT
  activity_id,
  resource_id,
  resource_name,
  sum(allowable_ev) AS allowable_cost,
  sum(to_date_cost) to_data_cost,
  sum(to_date_variance) to_date_var,
  sum(remaining_cost) remain_cost,
  sum(completion_cost) completion_cost,
  sum(cost_var) cost_var
FROM (SELECT
        budget.activity_id   AS activity_id,
        cost.resource_id ,
        budget.resource_name,
        sum(allowable_ev_cost) AS allowable_ev,
        sum(to_date_cost)      AS to_date_cost,
        sum(allowable_var)     AS to_date_variance,
        sum(remaining_cost)    AS remaining_cost,
        sum(completion_cost)   AS completion_cost,
        sum(cost_var)          AS  cost_var

      FROM cost_shadows AS cost
        LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
      WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                       FROM cost_shadows p
                                                       WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND
                                                             cost.period_id <= ?)
      GROUP BY 1,2,3) AS data
GROUP BY 1,2,3;', [$this->project->id, $chosen_period_id]))->map(function ($resource) {
            if (!isset($this->resourcesActivity[$resource->activity_id]['resources'])) {
                $this->resourcesActivity[$resource->activity_id] = ['resources' => []];
            }
            if (!isset($this->resourcesActivity[$resource->activity_id]['resources'][$resource->resource_id])) {
                $this->resourcesActivity[$resource->activity_id]['resources'][$resource->resource_id] =
                    [
                        'id' => $resource->resource_id
                        , 'name' => $resource->resource_name
                        , 'to_date_cost' => $resource->to_data_cost
                        , 'allowable_ev_cost' => $resource->allowable_cost
                        , 'allowable_var' => $resource->to_date_var
                        , 'remaining_cost' => $resource->remain_cost
                        , 'completion_cost' => $resource->completion_cost
                        , 'cost_var' => $resource->cost_var
                    ];
            }
            return $this->resourcesActivity;
        });


        collect(\DB::select('SELECT resource_id ,  SUM(budget_cost) AS budget_cost FROM break_down_resource_shadows
WHERE project_id=?
GROUP BY resource_id', [$project->id]))->map(function ($resource) {
            $this->budgetData->put($resource->resource_id, $resource->budget_cost);
        });

        $activity_divisions_tree = ActivityDivision::tree()->get();
        $tree = [];
        $report_name = 'Standard Activity';
        $concern = new CostConcernsController();
        $concerns = $concern->getConcernReport($project, $report_name);

        foreach ($activity_divisions_tree as $level) {
            $level_tree = $this->buildTree($level);
            $tree[] = $level_tree;
        }
        $total = $this->total;
        return view('reports.cost-control.standard_activity.standard_activity', compact('project', 'tree', 'concerns','total'));

    }

    protected function buildTree ($level)
    {
        $tree = ['id' => $level->id, 'name' => $level->name, 'children' => [], 'activities' => [],
            'to_data_cost' => 0,
            'to_date_allowable_cost' => 0,
            'cost_var' => 0,
            'remain_cost' => 0,
            'allowable_var' => 0,
            'completion_cost' => 0,
            'prev_cost' => 0,
            'prev_allowable' => 0,
            'prev_variance' => 0, 'budget_cost' => 0];


        if ($level->activities->count()) {
            $activities = $level->activities->whereIn('id', $this->project_activities->keys()->toArray());
            foreach ($activities as $activity) {
                if (!isset($tree['activities'][$activity->id])) {
                    $tree['activities'][$activity->id] = ['id' => $activity->id, 'name' => $activity->name, 'resources' => [],
                        'budget_cost' => $this->project_activities->get($activity->id),
                        'to_data_cost' => 0,
                        'to_date_allowable_cost' => 0,
                        'cost_var' => 0,
                        'remain_cost' => 0,
                        'allowable_var' => 0,
                        'completion_cost' => 0,
                        'prev_cost' => 0,
                        'prev_allowable' => 0,
                        'prev_variance' => 0,
                    ];
                }

                $resources = collect($this->resourcesActivity)->get($activity->id);

                if (count($resources['resources'])) {
                    foreach ($resources['resources'] as $resource) {
                        $tree['activities'][$activity->id]['resources'][$resource['id']] = [
                            'name' => $resource['name'],
                            'budget_cost' => $this->budgetData->get($resource['id']),
                            'to_data_cost' => $resource['to_date_cost'],
                            'to_date_allowable_cost' => $resource['allowable_ev_cost'],
                            'cost_var' => $resource['cost_var'],
                            'remain_cost' => $resource['remaining_cost'],
                            'allowable_var' => $resource['allowable_var'],
                            'completion_cost' => $resource['completion_cost'],
                            'prev_cost' => $this->prev_shadow->get($resource['id'])['prev_cost'],
                            'prev_allowable' => $this->prev_shadow->get($resource['id'])['prev_allowable'],
                            'prev_variance' => $this->prev_shadow->get($resource['id'])['prev_variance'],
                        ];

                        $tree['activities'][$activity->id]['to_data_cost'] += $resource['to_date_cost'];
                        $tree['activities'][$activity->id]['to_date_allowable_cost'] += $resource['allowable_ev_cost'];
                        $tree['activities'][$activity->id]['allowable_var'] += $resource['allowable_var'];
                        $tree['activities'][$activity->id]['remain_cost'] += $resource['remaining_cost'];
                        $tree['activities'][$activity->id]['cost_var'] += $resource['cost_var'];
                        $tree['activities'][$activity->id]['completion_cost'] += $resource['completion_cost'];
                        $tree['activities'][$activity->id]['prev_cost'] += $this->prev_shadow->get($resource['id'])['prev_cost'];
                        $tree['activities'][$activity->id]['prev_allowable'] += $this->prev_shadow->get($resource['id'])['prev_allowable'];
                        $tree['activities'][$activity->id]['prev_variance'] += $this->prev_shadow->get($resource['id'])['prev_variance'];
                    }
                }
                $tree['budget_cost'] += $tree['activities'][$activity->id]['budget_cost'];
                $tree['to_date_allowable_cost'] += $tree['activities'][$activity->id]['to_date_allowable_cost'];
                $tree['to_data_cost'] += $tree['activities'][$activity->id]['to_data_cost'];
                $tree['allowable_var'] += $tree['activities'][$activity->id]['allowable_var'];
                $tree['remain_cost'] += $tree['activities'][$activity->id]['remain_cost'];
                $tree['cost_var'] += $tree['activities'][$activity->id]['cost_var'];
                $tree['completion_cost'] += $tree['activities'][$activity->id]['completion_cost'];
                $tree['prev_cost'] += $tree['activities'][$activity->id]['prev_cost'];
                $tree['prev_allowable'] += $tree['activities'][$activity->id]['prev_allowable'];
                $tree['prev_variance'] += $tree['activities'][$activity->id]['prev_variance'];
            }
        }

        $this->total['budget_cost']+=$tree['budget_cost'];
        $this->total['to_date_allowable_cost']+=$tree['to_date_allowable_cost'];
        $this->total['to_data_cost']+=$tree['to_data_cost'];
        $this->total['allowable_var']+=$tree['allowable_var'];
        $this->total['remain_cost']+=$tree['remain_cost'];
        $this->total['cost_var']+=$tree['cost_var'];
        $this->total['completion_cost']+=$tree['completion_cost'];
        $this->total['prev_cost']+=$tree['prev_cost'];
        $this->total['prev_allowable']+=$tree['prev_allowable'];
        $this->total['prev_variance']+=$tree['prev_variance'];

        if ($level->children->count()) {
            $tree['children'] = $level->children->map(function ($childLevel) {
                return $this->buildTree($childLevel);
            });

            foreach ($tree['children'] as $child){
                $tree['budget_cost']+=$child['budget_cost'];
                $tree['to_date_allowable_cost']+=$child['to_date_allowable_cost'];
                $tree['to_data_cost']+=$child['to_data_cost'];
                $tree['allowable_var']+=$child['allowable_var'];
                $tree['remain_cost']+=$child['remain_cost'];
                $tree['cost_var']+=$child['cost_var'];
                $tree['completion_cost']+=$child['completion_cost'];
                $tree['prev_cost']+=$child['prev_cost'];
                $tree['prev_allowable']+=$child['prev_allowable'];
                $tree['prev_variance']+=$child['prev_variance'];
            }
        }


        return $tree;
    }


}