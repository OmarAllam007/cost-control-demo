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

        collect(\DB::select('SELECT  resource_id,resource_name, unit_price AS unit_price, SUM(budget_cost) AS budget_cost,sum(budget_unit) AS budget_unit
FROM break_down_resource_shadows
WHERE project_id = ?
GROUP BY resource_id , resource_name', [$project->id]))->map(function ($resource) {
            $this->resources->put($resource->resource_id, ['unit' => $resource->unit_price, 'budget_unit' => $resource->budget_unit, 'budget_cost' => $resource->budget_cost]);
        });
        collect(\DB::select('SELECT * FROM (
SELECT
  cost.resource_id,
  sum(cost.to_date_unit_price) /COUNT(cost.resource_id)                 AS to_date_unit_price,
  sum(curr_cost) current_cost,
  sum(curr_qty) current_qty,
  sum(curr_unit_price) current_unit_price,
  SUM(cost.to_date_qty)                     AS to_date_qty,
  SUM(cost.to_date_cost)                    AS to_date_cost,
  SUM(cost.allowable_ev_cost)               AS to_date_allowable_cost,
  SUM(cost.qty_var)               AS quantity_var,
  SUM(cost.allowable_var)                   AS allowable_var,
  sum(remaining_unit_price) /COUNT(cost.resource_id)             AS remaining_unit_price,
  SUM(cost.remaining_qty)                   AS remaining_qty,
  SUM(cost.remaining_cost)                  AS remain_cost,
  sum(completion_unit_price) /COUNT(cost.resource_id)           AS completion_unit_price,
  SUM(cost.completion_qty)                  AS completion_qty,
  SUM(cost.completion_cost)                 AS completion_cost,
  SUM(cost.cost_var)                        AS cost_var,
  SUM(cost.allowable_qty) AS allowable_qty
FROM cost_shadows AS cost

  LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                 FROM cost_shadows p
                                                 WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND
                                                       cost.period_id <= ?)
GROUP BY 1) AS data
GROUP BY 1;', [$project->id, $chosen_period_id]))->map(function ($resource) {
            $this->period_cost->put($resource->resource_id, [
                'to_date_unit_price' => $resource->to_date_unit_price ?? 0,
                'to_date_qty' => $resource->to_date_qty ?? 0,
                'to_date_cost' => $resource->to_date_cost ?? 0,
                'allowable_ev_cost' => $resource->to_date_allowable_cost ?? 0,
                'quantity_var' => $resource->quantity_var ?? 0,
                'allowable_var' => $resource->allowable_var ?? 0,
                'remaining_unit_price' => $resource->remaining_unit_price ?? 0,
                'remaining_qty' => $resource->remaining_qty ?? 0,
                'remaining_cost' => $resource->remain_cost ?? 0,
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