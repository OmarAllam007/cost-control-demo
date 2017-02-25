<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 21/12/16
 * Time: 11:11 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;

class CostSummery
{

    private $budget_cost;

    function getCostSummery(Project $project, $chosen_period_id)
    {

        $this->budget_cost = collect();
        collect(\DB::select('SELECT sh.resource_type_id  , t.name ,SUM(budget_cost) AS budget FROM break_down_resource_shadows sh , resource_types t
WHERE sh.project_id=? AND t.id = sh.resource_type_id
GROUP BY sh.resource_type_id ,t.name
ORDER BY  t.name', [$project->id]))->map(function ($type) {
            $this->budget_cost->put($type->name, $type->budget);
        });

        $shadows = \DB::select('SELECT
  sh.resource_type,
  SUM(c.to_date_cost) AS to_data_cost,
  SUM(c.allowable_ev_cost) AS to_date_allowable_cost,
  SUM(c.allowable_var) AS allowable_var,
  SUM(c.remaining_cost) AS remain_cost,
  SUM(c.completion_cost) AS completion_cost,
  SUM(c.cost_var) AS cost_var
FROM cost_shadows c, break_down_resource_shadows sh
WHERE c.project_id = ? AND c.period_id=?
      AND c.breakdown_resource_id = sh.breakdown_resource_id
GROUP BY sh.resource_type',[$project->id,$chosen_period_id]);
        $previousShadows = \DB::select('SELECT
  sh.resource_type,
  SUM(c.to_date_cost) as to_data_cost,
  SUM(c.allowable_ev_cost) as to_date_allowable_cost,
  SUM(c.allowable_var) AS allowable_var
  
FROM cost_shadows c, break_down_resource_shadows sh
WHERE c.project_id = ? AND c.period_id < ?
      AND c.breakdown_resource_id = sh.breakdown_resource_id
GROUP BY sh.resource_type',[$project->id,$chosen_period_id]);

        $data = [];
        foreach ($shadows as $shadow) {
            $data[$this->budget_cost->get($shadow->resource_type)] = [
                'name' => $shadow->resource_type,
                'budget_cost' => $this->budget_cost->get($shadow->resource_type),
                'to_date_cost' => $shadow->to_data_cost,
                'previous_cost' => 0,
                'previous_allowable' => 0,
                'previous_variance' => 0,
                'allowable_ev_cost' => $shadow->to_date_allowable_cost,
                'allowable_var' => $shadow->allowable_var,
                'cost_var' => $shadow->cost_var,
                'remaining_cost' => $shadow->remain_cost,
                'completion_cost' => $shadow->completion_cost,
            ];
            if (count($previousShadows)) {
                foreach ($previousShadows as $previousShadow) {
                    if (isset($data[$this->budget_cost->get($previousShadow->resource_type)])) {
                        $data[$this->budget_cost->get($previousShadow->resource_type)]['previous_cost'] += $previousShadow->to_data_cost; // to_date for previous
                        $data[$this->budget_cost->get($previousShadow->resource_type)]['previous_allowable'] += $previousShadow->to_date_allowable_cost; // to_date for previous
                        $data[$this->budget_cost->get($previousShadow->resource_type)]['previous_variance'] += $previousShadow->allowable_var; // to_date for previous
                    }
                }
            }
        }
        $data = collect($data)->sortBy('name');
        return view('reports.cost-control.cost_summery', compact('data', 'project'));
    }
}