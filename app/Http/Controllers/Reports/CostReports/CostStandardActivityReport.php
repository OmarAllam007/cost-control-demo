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

    function getStandardActivities(Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->prev_shadow = collect();
        $this->period_shadow = collect();
        $this->project_activities = collect();
        $this->budgetData = collect();


        collect(\DB::select('SELECT
  c.resource_id,
  SUM(c.to_date_cost)      AS to_data_cost,
  SUM(c.allowable_ev_cost) AS to_date_allowable_cost,
  SUM(c.cost_var)          AS cost_var,
  SUM(c.remaining_cost)    AS remain_cost,
  SUM(c.allowable_var)     AS allowable_var,
  SUM(c.completion_cost)   AS completion_cost
FROM cost_shadows c, break_down_resource_shadows sh
WHERE c.project_id = ? AND c.period_id < ?
      AND c.breakdown_resource_id = sh.breakdown_resource_id
GROUP BY c.resource_id', [$this->project->id, $chosen_period_id]))->map(function ($resource) {
            $this->prev_shadow->put($resource->resource_id, ['prev_cost' => $resource->to_data_cost
                , 'prev_allowabe' => $resource->to_date_allowable_cost
                , 'prev_variance' => $resource->cost_var
            ]);
        });
        collect(\DB::select('SELECT
  c.resource_id,
  SUM(c.to_date_cost)      AS to_data_cost,
  SUM(c.allowable_ev_cost) AS to_date_allowable_cost,
  SUM(c.cost_var)          AS cost_var,
  SUM(c.remaining_cost)    AS remain_cost,
  SUM(c.allowable_var)     AS allowable_var,
  SUM(c.completion_cost)   AS completion_cost
FROM cost_shadows c, break_down_resource_shadows sh
WHERE c.project_id = ? AND c.period_id = ?
      AND c.breakdown_resource_id = sh.breakdown_resource_id
GROUP BY c.resource_id', [$this->project->id, $chosen_period_id]))->map(function ($resource) {
            $this->period_shadow->put($resource->resource_id, ['to_data_cost' => $resource->to_data_cost
                , 'to_date_allowable_cost' => $resource->to_date_allowable_cost
                , 'cost_var' => $resource->cost_var
                , 'remain_cost' => $resource->remain_cost
                , 'allowable_var' => $resource->allowable_var
                , 'completion_cost' => $resource->completion_cost
            ]);
        });

        collect(\DB::select('SELECT DISTINCT sh.activity_id , sh.cost_account FROM break_down_resource_shadows sh
WHERE project_id=?', [$project->id]))->map(function ($activity) {
            $this->project_activities->put($activity->activity_id, $activity->cost_account);
        });
        collect(\DB::select('SELECT
 c.resource_id, activity_id,resource_name,
SUM(c.to_date_cost) to_data_cost, sum(c.allowable_ev_cost) allowable_cost,sum(allowable_var) to_date_var , sum(remaining_cost) remain_cost,
sum(completion_cost) completion_cost , sum(cost_var) cost_var
FROM break_down_resource_shadows sh
  JOIN cost_shadows c ON c.breakdown_resource_id = sh.breakdown_resource_id
WHERE c.project_id = ?  AND c.period_id=?
GROUP BY c.resource_id, activity_id', [$this->project->id, $chosen_period_id]))->map(function ($resource) {
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

        collect(\DB::select('SELECT resource_id ,  SUM(budget_cost) as budget_cost FROM break_down_resource_shadows
WHERE project_id=?
GROUP BY resource_id', [$project->id]))->map(function ($resource) {
            $this->budgetData->put($resource->resource_id,$resource->budget_cost);
        });
        $activity_divisions_tree = ActivityDivision::tree()->get();
        $tree = [];


        foreach ($activity_divisions_tree as $level) {
            $level_tree = $this->buildTree($level);
            $tree[] = $level_tree;
        }
        return view('reports.cost-control.standard_activity.standard_acticity', compact('project', 'tree'));

    }

    protected function buildTree($level)
    {
        $tree = ['id' => $level->id, 'name' => $level->name, 'children' => [], 'activities' => []];


        if ($level->children->count()) {
            $tree['children'] = $level->children->map(function ($childLevel) {
                return $this->buildTree($childLevel);
            });
        }

        if ($level->activities->count()) {
            $activities = $level->activities->whereIn('id', $this->project_activities->keys()->toArray());
            foreach ($activities as $activity) {
                $tree['activities'][$activity->id] = ['id' => $activity->id, 'name' => $activity->name, 'resources' => [], 'budget_cost' => 0];

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
                            'prev_allowabe' => $this->prev_shadow->get($resource['id'])['prev_allowabe'],
                            'prev_variance' => $this->prev_shadow->get($resource['id'])['prev_variance'],
                        ];
//                    $tree['activities'][$activity->id]['budget_cost'] += $resource->budget_cost;


                    }
                }
//                    $tree['activities'][$activity->id]['budget_cost'] += $tree['activities'][$activity->id]['cost_accounts'][$cost_account]['budget_cost'];
            }
        }


        return $tree;
    }


}