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

            if (!isset($data[ $resource->breakdown->std_activity->discipline ])) {
                $boq = Boq::where('cost_account', $resource->breakdown->cost_account)->first();

                $data[ $resource->breakdown->std_activity->discipline ] = [
                    'code' => $resource->breakdown->std_activity->code,
                    'name' => $resource->breakdown->std_activity->discipline,
                    'dry_qty' => $boq->quantity,
                    'budget_qty' => 0,
                    'dry_cost' => 0,
                    'budget_cost' => 0,
                    'budget_qty_eq' => 0,
                    'budget_cost_eq' => 0,

                ];


                $data[ $resource->breakdown->std_activity->discipline ]['budget_cost'] = $resource->budget_cost;
                $data[ $resource->breakdown->std_activity->discipline ]['budget_qty'] = $resource->budget_qty;
                $data[ $resource->breakdown->std_activity->discipline ]['dry_cost'] += $boq->dry_ur * $boq->quantity;


            } else {
                $data[ $resource->breakdown->std_activity->discipline ]['dry_cost'] += $boq->dry_ur * $boq->quantity;
                $data[ $resource->breakdown->std_activity->discipline ]['budget_cost'] += $resource->budget_cost;
                $data[ $resource->breakdown->std_activity->discipline ]['budget_qty'] += $resource->budget_qty;
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