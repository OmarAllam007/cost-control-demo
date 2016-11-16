<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 10:41 AM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Breakdown;
use App\BreakdownResource;
use App\Project;
use App\Survey;

class QtyAndCost
{
    public function compare(Project $project)
    {
        $break_down_resources = $project->breakdown_resources()->get();
        $data = [];
        $total = [
            'budget_qty_eq' => 0,
            'budget_cost_eq' => 0,
        ];
        $dry_cost = 0;
        $budget_cost = [];
        $breakdowns = $project->breakdowns()->get();
        foreach ($breakdowns as $breakdown) {
            $discipline = $breakdown->std_activity->discipline;
            $dry = Boq::where('cost_account', $breakdown->cost_account)->first();
            $budget_quantity = Survey::where('cost_account',$breakdown->cost_account)->first();
            if (!isset($data[ $discipline ])) {
                $data[ $discipline ] = [
                    'code' => $breakdown->std_activity->code,
                    'name' => $discipline,
                    'dry_qty' => $dry->quantity,
                    'budget_qty' => 0,
                    'cost_Account' => $breakdown->cost_account,
                    'dry_cost' => $dry->dry_ur,
                    'budget_cost' => 0,
                    'budget_qty_eq' => 0,
                    'budget_cost_eq' => 0,
                ];


            }
//            $data[ $discipline ]['dry_cost'] += is_nan($dry_cost) ? 0 : $dry_cost;
            foreach ($breakdown->resources as $resource) {
                $data[ $discipline ]['budget_cost'] += is_nan($resource->boq_unit_rate) ? 0 : $resource->boq_unit_rate;
                $data[ $discipline ]['budget_qty'] = is_nan($resource->budget_qty) ? 0 : $budget_quantity->budget_qty;
            }

        }


        foreach ($data as $key => $value) {
            $data[ $key ]['budget_qty_eq'] = ($data[ $key ]['budget_cost'] - $data[ $key ]['dry_cost']) * $data[ $key ]['budget_qty'];

            $data[ $key ]['budget_cost_eq'] = ($data[ $key ]['budget_qty'] - $data[ $key ]['dry_qty']) * $data[ $key ]['budget_cost'];

            $total['budget_qty_eq'] += $data[ $key ]['budget_qty_eq'];
            $total['budget_cost_eq'] += $data[ $key ]['budget_cost_eq'];
        }
        return view('reports.qty_and_cost', compact('data', 'total', 'project'));
    }

}