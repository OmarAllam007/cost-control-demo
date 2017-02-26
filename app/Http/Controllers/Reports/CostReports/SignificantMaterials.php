<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 21/12/16
 * Time: 10:05 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\Resources;
use App\ResourceType;

class SignificantMaterials
{
    protected $project;
    protected $resources;
    protected $period_cost;

    public function getTopHighPriorityMaterials(Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->resources = collect();
        $this->period_cost = collect();
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
        set_time_limit(300);
        $resources = Resources::where('project_id', $project->id)->whereNotNull('top_material')->get();
        $data = [];
        foreach ($resources as $resource) {
            if (!isset($data[$resource->top_material])) {
                $data[$resource->top_material] = [
                    'resource_type_id' => $resource->types->id,
                    'resource_type' => trim($resource->top_material),
                    'resources' => [],
                ];
            }
            if (!isset($data[$resource->top_material]['resources'][$resource->name])) {
                $data[$resource->top_material]['resources'][$resource->name] = [
                    'name' => $resource->name,
                    'unit_price' => $this->resources->get($resource->id)['unit'] ?? 0
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
            }

        }
        return $data;
    }


}