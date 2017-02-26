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
    protected $period_id;

    public function getResourceCodeReport(Project $project, $chosen_period_id)
    {
        set_time_limit(300);
        $this->project = $project;
        $this->resources = collect();
        $this->period_cost = collect();
        $this->prev_cost = collect();
        $this->types = collect();
        $this->period_id = $chosen_period_id;


        collect(\DB::select('SELECT  resource_id,resource_name, sum(unit_price) AS unit_price, SUM(budget_cost) AS budget_cost,sum(budget_unit) AS budget_unit
FROM break_down_resource_shadows
WHERE project_id = ?
GROUP BY resource_id , resource_name', [$project->id]))->map(function ($resource) {
            $this->resources->put($resource->resource_id, ['unit' => $resource->unit_price, 'budget_unit' => $resource->budget_unit, 'budget_cost' => $resource->budget_cost]);
        });

        collect(\DB::select('SELECT
  c.resource_id,
  SUM(c.to_date_unit_price)              AS to_data_unit_price,
  SUM(c.to_date_qty)                     AS to_date_qty,
  SUM(c.to_date_cost)                    AS to_data_cost,
  SUM(c.allowable_ev_cost)               AS to_date_allowable_cost,
  SUM(c.qty_var)               AS quantity_var,
  SUM(c.allowable_var)                   AS allowable_var,
  SUM(c.remaining_unit_price)            AS remaining_unit_price,
  SUM(c.remaining_qty)                   AS remaining_qty,
  SUM(c.remaining_cost)                  AS remain_cost,
  SUM(c.completion_unit_price)           AS completion_unit_price,
  SUM(c.completion_qty)                  AS completion_qty,
  SUM(c.completion_cost)                 AS completion_cost,
  SUM(c.cost_var)                        AS cost_var,
  SUM(c.pw_index) / COUNT(c.resource_id) AS pw_index
FROM cost_shadows c
WHERE c.project_id = ? AND c.period_id = ?
GROUP BY c.resource_id', [$project->id, $chosen_period_id]))->map(function ($resource) {
            $this->period_cost->put($resource->resource_id, [
                'unit_price' => $resource->unit_price ?? 0,
                'budget_unit' => $resource->budget_unit ?? 0,
                'budget_cost' => $resource->budget_cost ?? 0,
                'to_date_unit_price' => $resource->to_date_unit_price ?? 0,
                'to_date_qty' => $resource->to_date_qty ?? 0,
                'to_date_cost' => $resource->to_date_cost ?? 0,
                'allowable_ev_cost' => $resource->allowable_ev_cost ?? 0,
                'quantity_var' => $resource->quantity_var ?? 0,
                'allowable_var' => $resource->allowable_var ?? 0,
                'remaining_unit_price' => $resource->remaining_unit_price ?? 0,
                'remaining_qty' => $resource->remaining_qty ?? 0,
                'remaining_cost' => $resource->remaining_cost ?? 0,
                'completion_unit_price' => $resource->completion_unit_price ?? 0,
                'completion_qty' => $resource->completion_qty ?? 0,
                'completion_cost' => $resource->completion_cost ?? 0,
                'cost_var' => $resource->cost_var ?? 0,
                'pw_index' => $resource->pw_index ?? 0,

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
        $tree = [];
        $types = \Cache::has('resources-tree') ? \Cache::get('resources-tree') : ResourceType::tree()->get();
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
        $tree = ['id' => $type['id'], 'name' => $type['name'], 'children' => [], 'resources' => [], 'budget_cost' => 0, 'top' => []];
        $resources = $this->types->get($type['id']);
        if (count($resources)) {
            foreach ($resources as $resource) {
                $tree['resources'][$resource['id']] = [
                    'id' => $resource['id']
                    , 'name' => $resource['name']
                    , 'unit_price' => $this->resources->get($resource->id)['unit'] ?? 0
                    , 'budget_unit' => $this->resources->get($resource->id)['budget_unit'] ?? 0
                    , 'budget_cost' => $this->resources->get($resource->id)['budget_cost'] ?? 0
                    , 'to_date_unit_price' => $this->period_cost->get($resource['id'])['to_date_unit_price'] ?? 0
                    , 'to_date_qty' => $this->period_cost->get($resource['id'])['to_date_qty'] ?? 0
                    , 'to_date_cost' => $this->period_cost->get($resource['id'])['to_date_cost'] ?? 0
                    , 'allowable_ev_cost' => $this->period_cost->get($resource['id'])['allowable_ev_cost'] ?? 0
                    , 'quantity_var' => $this->period_cost->get($resource['id'])['quantity_var'] ?? 0
                    , 'allowable_var' => $this->period_cost->get($resource['id'])['allowable_var'] ?? 0
                    , 'remaining_unit_price' => $this->period_cost->get($resource['id'])['remaining_unit_price'] ?? 0
                    , 'remaining_qty' => $this->period_cost->get($resource['id'])['remaining_qty'] ?? 0
                    , 'remaining_cost' => $this->period_cost->get($resource['id'])['remaining_cost'] ?? 0
                    , 'completion_unit_price' => $this->period_cost->get($resource['id'])['completion_unit_price'] ?? 0
                    , 'completion_qty' => $this->period_cost->get($resource['id'])['completion_qty'] ?? 0
                    , 'completion_cost' => $this->period_cost->get($resource['id'])['completion_cost'] ?? 0
                    , 'cost_var' => $this->period_cost->get($resource['id'])['cost_var'] ?? 0
                    , 'pw_index' => $this->period_cost->get($resource['id'])['pw_index'] ?? 0


                ];
                $tree['budget_cost'] += $this->resources->get($resource['id'])['budget_cost'];
            }
        }

        $tree['resources'] = collect($tree['resources'])->sortBy('code');
        if ($type['name'] == '03.MATERIAL') {
            $top = new SignificantMaterials();
            $tree['top'] = $top->getTopHighPriorityMaterials($this->project, $this->period_id);
        }

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