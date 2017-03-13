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
    private $project;

    function getCostSummery(Project $project, $chosen_period_id)
    {
        $this->project = $project;
        $this->budget_cost = collect();
        $this->cost_data = collect();
        $to_date_cost_var_chart = [];
        $at_comp_cost_var_chart = [];
        $prev_period = Period::where('project_id', $project->id)->where('id', '<', $chosen_period_id)->get()->sortByDesc('id')->first();
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
  SUM(sh.budget_cost) AS to_data_cost FROM break_down_resource_shadows sh
WHERE sh.project_id = ? 
GROUP BY sh.resource_type,sh.resource_type_id', [$project->id]);

        collect(\DB::select('SELECT
  sh.resource_type_id,
  SUM(c.to_date_cost) AS to_date_cost,
  SUM(c.allowable_ev_cost) AS to_date_allowable_cost,
  SUM(c.allowable_var) AS allowable_var,
  SUM(c.remaining_cost) AS remain_cost,
  SUM(c.completion_cost) AS completion_cost,
  SUM(c.cost_var) AS cost_var
FROM cost_shadows c, break_down_resource_shadows sh
WHERE c.project_id = ? AND c.period_id=?
  AND c.breakdown_resource_id = sh.breakdown_resource_id
GROUP BY sh.resource_type_id', [$project->id, $chosen_period_id]))->map(function ($resource) {
            $this->cost_data->put($resource->resource_type_id, ['to_date_cost' => $resource->to_date_cost,
                'allowable_var' => $resource->allowable_var, 'remain_cost' => $resource->remain_cost, 'to_date_allowable_cost' => $resource->to_date_allowable_cost,
                'completion_cost' => $resource->remain_cost, 'cost_var' => $resource->cost_var

            ]);
        });

        if (isset($prev_period->id)) {
            $previousShadows = \DB::select('SELECT
  sh.resource_type,sh.resource_type_id,
  SUM(c.to_date_cost) AS to_data_cost,
  SUM(c.allowable_ev_cost) AS to_date_allowable_cost,
  SUM(c.allowable_var) AS allowable_var
  
FROM cost_shadows c, break_down_resource_shadows sh
WHERE c.project_id = ? AND c.period_id = ?
      AND c.breakdown_resource_id = sh.breakdown_resource_id
GROUP BY sh.resource_type , sh.resource_type_id', [$project->id, $prev_period->id]);

        }

        $data = [];

        $total = ['budget_cost' => 0, 'to_date_cost' => 0, 'previous_cost' => 0,
            'previous_allowable' => 0, 'previous_variance' => 0, 'allowable_ev_cost' => 0, 'allowable_var' => 0,
            'cost_var' => 0, 'remaining_cost' => 0, 'completion_cost' => 0];

        foreach ($shadows as $shadow) {
            if (!isset($data[$shadow->resource_type_id])) {
                $data[$shadow->resource_type_id] = [
                    'name' => $shadow->resource_type,
                    'budget_cost' => $this->budget_cost->get($shadow->resource_type_id),
                    'to_date_cost' => $this->cost_data->get($shadow->resource_type_id)['to_date_cost'],
                    'previous_cost' => 0,
                    'previous_allowable' => 0,
                    'previous_variance' => 0,
                    'allowable_ev_cost' => $this->cost_data->get($shadow->resource_type_id)['to_date_allowable_cost'],
                    'allowable_var' => $this->cost_data->get($shadow->resource_type_id)['allowable_var'],
                    'cost_var' => $this->cost_data->get($shadow->resource_type_id)['cost_var'],
                    'remaining_cost' => $this->cost_data->get($shadow->resource_type_id)['remain_cost'],
                    'completion_cost' => $this->cost_data->get($shadow->resource_type_id)['completion_cost'],
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


            $total['budget_cost'] += $this->budget_cost->get($shadow->resource_type);
            $total['to_date_cost'] += $this->cost_data->get($shadow->resource_type_id)['to_date_cost'];
            $total['allowable_ev_cost'] += $this->cost_data->get($shadow->resource_type_id)['to_date_allowable_cost'];
            $total['allowable_var'] += $this->cost_data->get($shadow->resource_type_id)['allowable_var'];
            $total['cost_var'] += $this->cost_data->get($shadow->resource_type_id)['cost_var'];
            $total['remaining_cost'] += $this->cost_data->get($shadow->resource_type_id)['remain_cost'];
            $total['completion_cost'] += $this->cost_data->get($shadow->resource_type_id)['completion_cost'];


        }
        if (isset($prev_period->id)&&count($previousShadows)) {
            foreach ($previousShadows as $previousShadow) {
                if (isset($data[$previousShadow->resource_type_id])) {
                    $data[$previousShadow->resource_type_id]['previous_cost'] += $previousShadow->to_data_cost; // to_date for previous
                    $data[$previousShadow->resource_type_id]['previous_allowable'] += $previousShadow->to_date_allowable_cost; // to_date for previous
                    $data[$previousShadow->resource_type_id]['previous_variance'] += $previousShadow->allowable_var; // to_date for previous
                }

                $total['previous_cost'] += $previousShadow->to_data_cost;
                $total['previous_allowable'] += $previousShadow->to_date_allowable_cost;
                $total['previous_variance'] += $previousShadow->allowable_var;


            }
        }

        $data = collect($data)->sortBy('name');

        return view('reports.cost-control.cost_summery', compact('data', 'project', 'total', 'to_date_cost_var_chart'
            , 'at_comp_cost_var_chart', 'report_name', 'concerns'));
    }
}