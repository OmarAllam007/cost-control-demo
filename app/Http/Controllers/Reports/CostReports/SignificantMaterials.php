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
    private $project;

    public function getTopHighPriorityMaterials(Project $project, $chosen_period_id)
    {
        $this->project = $project;
        set_time_limit(300);
        $resources = Resources::where('project_id', $project->id)->whereNotNull('top_material')->get();
        $data = [];
        foreach ($resources as $resource) {
            $budget_cost = \DB::select('SELECT
  sh.resource_name,
  SUM(sh.budget_cost) AS budget_cost
FROM break_down_resource_shadows sh
WHERE sh.project_id = ?
      AND sh.resource_id=?
GROUP BY sh.resource_name', [$project->id, $resource->id]);
            $shadow_cost = \DB::select('SELECT
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
      AND c.resource_id = ?
GROUP BY c.resource_id', [$project->id, $chosen_period_id, $resource->id]);
            $prev_cost = \DB::select('SELECT
  c.resource_id,
  SUM(c.to_date_cost)      AS to_data_cost,
  SUM(c.allowable_ev_cost) AS to_date_allowable_cost,
  SUM(c.cost_var)          AS cost_var
FROM cost_shadows c, break_down_resource_shadows sh
WHERE c.project_id = ? AND c.period_id < ?
      AND c.breakdown_resource_id = sh.breakdown_resource_id
      AND c.resource_id = ?
GROUP BY c.resource_id', [$project->id, $chosen_period_id, $resource->id]);

            if ($budget_cost) {
                if (!isset($data[$resource->top_material])) {
                    $data[$resource->top_material] = [
                        'resource_type_id'=>$resource->types->id,
                        'resource_type' => trim($resource->top_material),
                        'resources' => [],
                    ];
                }
                if (!isset($data[$resource->top_material]['resources'][$resource->name])) {
                    $data[$resource->top_material]['resources'][$resource->name] = [
                        'resource_name' => trim($resource->name),
                        'budget_cost' => $budget_cost[0]->budget_cost,
                        'previous_cost' => $prev_cost[0]->to_data_cost ?? 0,
                        'previous_allowable' => $prev_cost[0]->to_date_allowable_cost ??0,
                        'previous_variance' => $prev_cost[0]->cost_var ?? 0 ,
                        'to_date_cost' => $shadow_cost[0]->to_data_cost ?? 0,
                        'allowable_ev_cost' => $shadow_cost[0]->to_date_allowable_cost ?? 0,
                        'cost_var' => $shadow_cost[0]->cost_var ?? 0,
                        'remaining_cost' => $shadow_cost[0]->remain_cost ?? 0,
                        'allowable_var' => $shadow_cost[0]->allowable_var ?? 0,
                        'completion_cost' => $shadow_cost[0]->completion_cost ?? 0,
                    ];
                }
            }
        }
        return view('reports.cost-control.significant_materials',compact('data','project'));
    }


}