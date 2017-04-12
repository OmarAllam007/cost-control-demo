<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 20/12/16
 * Time: 03:45 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\CostShadow;
use App\Project;

class ProjectInformation
{
    function getProjectInformation (Project $project, $period_id)
    {
        $shadow = \DB::select('SELECT
  sum(allowable_ev) AS  allowable_cost,
  sum(to_date_cost)     to_date_cost,
  sum(cost_var)         cost_var
FROM (SELECT


        sum(allowable_ev_cost)  AS allowable_ev,
        sum(to_date_cost)       AS to_date_cost,
        sum(cost_var)           AS cost_var

      FROM cost_shadows AS cost
        LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
      WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                       FROM cost_shadows p
                                                       WHERE p.breakdown_resource_id = cost.breakdown_resource_id) AND cost.period_id <= ?) AS DATA;', [$project->id, $period_id]);


        $data = [
            'actual_cost' => 0,
            'allowable_cost' => 0,
            'cpi' => 0,
            'cost_variance' => 0,
        ];

        $data['actual_cost'] = $shadow[0]->to_date_cost;
        $data['allowable_cost'] = $shadow[0]->allowable_cost;


        return view('reports.cost-control.project_information', compact('project', 'data', 'allowable_cost', 'actual_cost'));
    }

}