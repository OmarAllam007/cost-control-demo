<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 26/12/16
 * Time: 11:51 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\BusinessPartner;
use App\CostShadow;
use App\Project;
use App\Resources;
use App\ResourceType;

class ResourceCodeReport
{
    protected $project;
    protected $resources;
    protected $period_cost;
    protected $prev_cost;

    public function getResourceCodeReport(Project $project, $chosen_period_id)
    {
        set_time_limit(300);
        $this->project = $project;
        $this->resources = collect();
        $this->period_cost = collect();
        $this->prev_cost = collect();
        $this->types = collect();

        $tree = [];
        $types= \Cache::has('resources-tree')?\Cache::get('resources-tree') : ResourceType::tree()->get();
        collect(\DB::select('SELECT  resource_id,measure_unit, SUM(budget_cost) AS budget_cost,sum(budget_unit) AS budget_unit 
FROM break_down_resource_shadows
WHERE project_id = ' . $project->id . '
GROUP BY resource_id , measure_unit'))->map(function ($resource) {
            $this->resources->put($resource->resource_id, ['unit' => $resource->measure_unit, 'budget_unit' => $resource->budget_unit, 'budget_cost' => $resource->budget_cost]);

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
GROUP BY c.resource_id', [$project->id, $chosen_period_id]))->map(function ($resource) {
            $this->period_cost->put($resource->resource_id, ['to_date_cost' => $resource->to_date_cost ?? 0, 'to_date_allowable_cost' => $resource->to_date_allowable_cost ?? 0
                , 'cost_var' => $resource->cost_var ?? 0
                , 'remain_cost' => $resource->remain_cost ?? 0
                , 'allowable_var' => $resource->allowable_var ?? 0
                , 'completion_cost' => $resource->completion_cost ?? 0
            ]);
        });
        collect(\DB::select('SELECT
  c.resource_id,
  SUM(c.to_date_cost)      AS to_data_cost,
  SUM(c.allowable_ev_cost) AS to_date_allowable_cost,
  SUM(c.cost_var)          AS cost_var
FROM cost_shadows c
WHERE c.project_id = ? AND c.period_id < ?
GROUP BY c.resource_id', [$project->id, $chosen_period_id]))->map(function ($resource) {
            $this->prev_cost->put($resource->resource_id, ['to_date_cost' => $resource->to_data_cost ?? 0, 'to_date_allowable_cost' => $resource->to_date_allowable_cost ?? 0
                , 'cost_var' => $resource->cost_var ?? 0
            ]);
        });

        $this->partners = BusinessPartner::all()->keyBy('id')->map(function ($partner) {
            return $partner->name;
        });

        $this->types = ResourceType::whereHas('resources', function ($q) {
            $q->where('project_id', $this->project->id);
        })->get()->keyBy('id')->map(function ($type) {
            return $type->resources->where('project_id', $this->project->id);
        });

        foreach ($types as $type) {
            $treeType = $this->buildTypeTree($type);
            $tree[] = $treeType;
        }
        return view('reports.cost-control.resource_code.resource_code', compact('project', 'tree'));
    }


    /**
     * @param $type
     * @return array
     */
    private function buildTypeTree($type)
    {
        $tree = ['id' => $type['id'], 'name' => $type['name'], 'children' => [], 'resources' => [], 'budget_cost' => 0];


        $resources = $this->types->get($type['id']);
        if (count($resources)) {
            foreach ($resources as $resource) {
                $tree['resources'][$resource['id']] = ['id' => $resource['id']
                    , 'name' => $resource['name']
                    , 'budget_cost' => $this->resources->get($resource['id'])['budget_cost'] ?? 0
                    , 'prev_cost' => $this->prev_cost->get($resource['id'])['to_date_cost'] ?? 0
                    , 'prev_allowable_cost' => $this->prev_cost->get($resource['id'])['to_date_allowable_cost'] ?? 0
                    , 'prev_var' => $this->prev_cost->get($resource['id'])['cost_var'] ?? 0
                    , 'to_data_cost' => $this->period_cost->get($resource['id'])['to_data_cost'] ?? 0
                    , 'to_date_allowable_cost' => $this->period_cost->get($resource['id'])['cost_var'] ?? 0
                    , 'cost_var' => $this->period_cost->get($resource['id'])['cost_var'] ?? 0
                    , 'remain_cost' => $this->period_cost->get($resource['id'])['remain_cost'] ?? 0
                    , 'allowable_var' => $this->period_cost->get($resource['id'])['allowable_var'] ?? 0
                    , 'completion_cost' => $this->period_cost->get($resource['id'])['completion_cost'] ?? 0

                ];
                $tree['budget_cost'] += $this->resources->get($resource['id'])['budget_cost'];
            }
        }

        $tree['resources'] = collect($tree['resources'])->sortBy('code');

        if (collect($type['children'])->count()) {
            $tree['children'] = collect($type['children'])->map(function ($child) use ($tree) {
                $subtree = $this->buildTypeTree($child);
                return $subtree;
            });

            foreach ($tree['children'] as $child) {
                $tree['budget_cost'] += $child['budget_cost'];
            }

        }

        return $tree;
    }

}