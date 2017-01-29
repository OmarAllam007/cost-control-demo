<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 9:00 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Project;
use App\Survey;

class BudgetCostDryCostByDiscipline
{
    public function compareBudgetCostDryCostDiscipline(Project $project)
    {
        set_time_limit(300);
        $break_downs = $project->breakdown_resources()->with('breakdown.std_activity','breakdown.std_activity.division','breakdown.wbs_level','template_resource.resource')->get();

        $data = [];
        $total = [
            'dry_cost' => 0,
            'budget_cost' => 0,
            'difference' => 0,
            'increase' => 0,
        ];
        foreach ($break_downs as $break_down) {
            $discipline = $break_down->breakdown->std_activity->discipline;
            $code = $break_down->breakdown->std_activity->code;
            $budget_cost = $break_down->budget_cost;
            if (!isset($data[ $discipline ])) {
                $data[ $discipline ] = [
                    'code' => $code,
                    'name' => $discipline,
                    'dry_cost' => 0,
                    'budget_cost' => 0,
                    'difference' => 0,
                    'increase' => 0,
                ];
                $boq = Boq::where('cost_account', $break_down->breakdown->cost_account)->first();
                $dry_cost = $boq->dry_ur * $boq->quantity;
                $data[ $discipline ]['budget_cost'] = is_nan($budget_cost)?0:$budget_cost;
                $data[ $discipline ]['dry_cost'] = is_nan($dry_cost)?0:$dry_cost;
            } else {
                $data[ $discipline ]['budget_cost'] += is_nan($budget_cost)?0:$budget_cost;
                $data[ $discipline ]['dry_cost'] += is_nan($dry_cost)?0:$dry_cost;
            }
        }
        foreach ($data as $key => $value) {
            if ($data[ $key ]['dry_cost']) {
                $data[ $key ]['difference'] += ($data[ $key ]['budget_cost'] - $data[ $key ]['dry_cost']);
                $data[ $key ]['increase'] += ($data[ $key ]['difference'] / $data[ $key ]['dry_cost']);
                $total['difference'] += $data[ $key ]['difference'];
                $total['dry_cost'] += $data[ $key ]['dry_cost'];
                $total['budget_cost'] += $data[ $key ]['budget_cost'];
                $total['increase'] += $data[ $key ]['increase'];
            }
        }
        return view('reports.budget_cost_dry_cost_by_discipline', compact('data', 'total', 'project'));
    }

}