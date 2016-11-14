<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 10:41 AM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Project;

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
        foreach ($break_down_resources as $resource) {
            $discipline = $resource->breakdown->std_activity->discipline;
            $boq = Boq::where('cost_account', $resource->breakdown->cost_account)->first();
            $dry_cost = $boq->dry_ur * $boq->quantity;
            if (!isset($data[ $discipline ])) {
                $data[ $discipline ] = [
                    'code' => $resource->breakdown->std_activity->code,
                    'name' => $discipline,
                    'dry_qty' => $boq->quantity,
                    'budget_qty' => 0,
                    'dry_cost' => 0,
                    'budget_cost' => 0,
                    'budget_qty_eq' => 0,
                    'budget_cost_eq' => 0,
                ];

                $data[ $discipline ]['budget_cost'] = is_nan($resource->budget_cost)?0:$resource->budget_cost;
                $data[ $discipline ]['budget_qty'] = is_nan($resource->budget_qty)?0:$resource->budget_qty;
                $data[ $discipline ]['dry_cost'] = is_nan($dry_cost)?0:$dry_cost;
            } else {

                $data[ $discipline ]['budget_cost'] += is_nan($resource->budget_cost)?0:$resource->budget_cost;
                $data[ $discipline ]['budget_qty'] += is_nan($resource->budget_qty)?0:$resource->budget_qty;
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