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
use App\CostConcerns;
use App\CostShadow;
use App\Http\Controllers\CostConcernsController;
use App\Period;
use App\Project;
use App\ResourceType;

class CostSummery
{

    private $budget_cost;
    private $cost_data;
    private $prev_data;
    private $project;

    function getCostSummery (Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->budget_cost = collect();
        $this->cost_data = collect();
        $this->prev_data = collect();
        $to_date_cost_var_chart = [];
        $at_comp_cost_var_chart = [];

        $report_name = 'Cost Summary Report';

        $concern = new CostConcernsController();
        $concerns = $concern->getConcernReport($project, $report_name);

        collect(\DB::select('SELECT sh.resource_type_id  , t.name ,SUM(budget_cost) AS budget FROM break_down_resource_shadows sh , resource_types t
WHERE sh.project_id=? AND t.id = sh.resource_type_id
GROUP BY sh.resource_type_id ,t.name
ORDER BY  t.name', [$project->id]))->map(function ($type) {
            $this->budget_cost->put($type->resource_type_id, $type->budget);
        });

        $shadows = \DB::select('SELECT
  sh.resource_type,sh.resource_type_id,
  SUM(sh.budget_cost) AS budget_cost FROM break_down_resource_shadows sh
WHERE sh.project_id = ? 
GROUP BY sh.resource_type,sh.resource_type_id', [$project->id]);

        collect(\DB::select('SELECT
  resource_type_id,
  sum(allowable_ev) AS  allowable_cost,
  sum(to_date_cost)     to_date_cost,
  sum(to_date_variance) to_date_var,
  sum(remaining_cost)   remain_cost,
  sum(completion_cost)  comp_cost,
  sum(cost_var)         cost_var
FROM (SELECT
        budget.resource_type_id AS resource_type_id,
    
        sum(allowable_ev_cost) AS allowable_ev,
        sum(to_date_cost)       AS to_date_cost,
        sum(allowable_var)      AS to_date_variance,
        sum(remaining_cost)     AS remaining_cost,
        sum(completion_cost)    AS completion_cost,
        sum(cost_var)           AS cost_var
        

      FROM cost_shadows AS cost
        LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
      WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                       FROM cost_shadows p
                                                       WHERE p.breakdown_resource_id = cost.breakdown_resource_id  AND
            p.period_id <= ?)
      GROUP BY 1) AS DATA
GROUP BY 1;', [$project->id,$chosen_period_id]))->map(function ($resource) {
            $this->cost_data->put($resource->resource_type_id,
                [
                    'to_date_cost' => $resource->to_date_cost,
                    'allowable_var' => $resource->to_date_var,
                    'remain_cost' => $resource->remain_cost,
                    'to_date_allowable_cost' => $resource->allowable_cost,
                    'completion_cost' => $resource->comp_cost,
                    'cost_var' => $resource->cost_var
                ]);
        });

        collect(\DB::select('SELECT
  resource_type_id,
  sum(allowable_ev) AS allowable_cost,
  sum(to_date_cost) to_date_cost,
  sum(to_date_variance) to_date_var
FROM (SELECT
        budget.resource_type_id   AS resource_type_id,
        sum(to_date_cost)      AS to_date_cost,
        sum(allowable_ev_cost) AS allowable_ev,
        sum(allowable_var)     AS to_date_variance
        
      FROM cost_shadows AS cost
        LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
      WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                       FROM cost_shadows p
                                                       WHERE p.breakdown_resource_id = cost.breakdown_resource_id and p.period_id < ?)
      GROUP BY 1) AS DATA
GROUP BY 1;', [$project->id, $chosen_period_id]))->map(function ($resource) {
            $this->prev_data->put($resource->resource_type_id,
                [
                    'to_date_cost' => $resource->to_date_cost,
                    'allowable_var' => $resource->to_date_var,
                    'to_date_allowable_cost' => $resource->allowable_cost,
                ]);
        });
        $data = [];

        $total = ['budget_cost' => 0, 'to_date_cost' => 0, 'previous_cost' => 0,
            'previous_allowable' => 0, 'previous_variance' => 0, 'allowable_ev_cost' => 0, 'allowable_var' => 0,
            'cost_var' => 0, 'remaining_cost' => 0, 'completion_cost' => 0];

        $shadows = CostShadow::where('project_id',$project->id)->where('period_id','<=',5)->get();
        $to_date_cost = 0 ;
        foreach ($shadows as $shadow){
            $to_date_cost +=$shadow->curr_cost;
        }

        foreach ($shadows as $shadow) {
            if (!isset($data[$shadow->resource_type_id])) {
                $data[$shadow->resource_type_id] = [
                    'name' => $shadow->resource_type,
                    'budget_cost' => $this->budget_cost->get($shadow->resource_type_id),
                    'to_date_cost' => $this->cost_data->get($shadow->resource_type_id)['to_date_cost'],
                    'previous_cost' => $this->prev_data->get($shadow->resource_type_id)['to_date_cost'],
                    'previous_allowable' => $this->prev_data->get($shadow->resource_type_id)['to_date_allowable_cost'],
                    'previous_variance' => $this->prev_data->get($shadow->resource_type_id)['allowable_var'],
                    'allowable_ev_cost' => $this->cost_data->get($shadow->resource_type_id)['to_date_allowable_cost'],
                    'allowable_var' => $this->cost_data->get($shadow->resource_type_id)['allowable_var'],
                    'remaining_cost' => $this->cost_data->get($shadow->resource_type_id)['remain_cost'],
                    'completion_cost' => $this->cost_data->get($shadow->resource_type_id)['completion_cost'],
                    'cost_var' => $this->budget_cost->get($shadow->resource_type_id)-$this->cost_data->get($shadow->resource_type_id)['completion_cost'],
                ];
            }
            $to_date_cost_var_chart[$shadow->resource_type] = [
                'name' => $shadow->resource_type,
                'to_date_cost_var' => $this->cost_data->get($shadow->resource_type_id)['allowable_var'],
            ];

            $at_comp_cost_var_chart[$shadow->resource_type] = [
                'name' => $shadow->resource_type,
                'at_comp_cost_var' => $this->cost_data->get($shadow->resource_type_id)['cost_var'],
            ];

        }


        $data = collect($data)->sortBy('name');

        return view('reports.cost-control.cost_summery', compact('data', 'project', 'total', 'to_date_cost_var_chart'
            , 'at_comp_cost_var_chart', 'report_name', 'concerns'));
    }
}