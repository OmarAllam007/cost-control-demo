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
        $break_downs = $project->breakdowns()->get();
        $data = [];
        $total = [
            'budget_qty_eq' => 0,
            'budget_cost_eq' => 0,
        ];
        foreach ($break_downs as $break_down) {
            $boq = Boq::where('cost_account', $break_down->cost_account)->first();

                if (!isset($data[ $break_down->std_activity->discipline ])) {
                    $data[ $break_down->std_activity->discipline ] = [
                        'code' => $break_down->std_activity->code,
                        'name' => $break_down->std_activity->discipline,
                        'dry_qty' => $boq->quantity,
                        'budget_qty' => 0,
                        'dry_cost' => 0,
                        'budget_cost' => 0,
                        'budget_qty_eq' => 0,
                        'budget_cost_eq' => 0,

                    ];

                $data[ $break_down->std_activity->discipline ]['dry_cost'] += $boq->sum(\DB::raw('dry_ur * quantity'));

                foreach ($break_down->resources as $resource) {
                    $data[ $break_down->std_activity->discipline ]['budget_cost'] += $resource->budget_cost;
                    $data[ $break_down->std_activity->discipline ]['budget_qty'] += $resource->budget_qty;
                }

            }
        }
        foreach ($data as $key => $value) {
            $data[ $key ]['budget_qty_eq'] = ($data[$key]['budget_cost'] - $data[$key]['dry_cost'])* $data[$key]['budget_qty'];

            $data[ $key ]['budget_cost_eq'] = ($data[$key]['budget_qty'] - $data[$key]['dry_qty'])* $data[$key]['budget_cost'];

            $total['budget_qty_eq'] +=$data[ $key ]['budget_qty_eq'];
            $total['budget_cost_eq'] +=$data[ $key ]['budget_cost_eq'];
        }
        return view('reports.qty_and_cost', compact('data','total','project'));
    }

}