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
            'dry_qty_eq' => 0,
            'budget_cost_eq' => 0,
        ];
        foreach ($break_downs as $break_down) {
            $boqs = Boq::where('cost_account', $break_down->cost_account);
            foreach ($boqs->get() as $boq) {
                if (!isset($data[ $boq->type ])) {
                    $data[ $boq->type ] = [
                        'code' => $boq->item_code,
                        'name' => $boq->type,
                        'dry_qty' => $boq->quantity,
                        'budget_qty' => 0,
                        'dry_cost' => 0,
                        'budget_cost' => 0,
                        'dry_qty_eq' => 0,
                        'budget_cost_eq' => 0,

                    ];
                }
                $data[ $boq->type ]['dry_cost'] += $boqs->sum(\DB::raw('dry_ur * quantity'));

                foreach ($break_down->resources as $resource) {
                    $data[ $boq->type ]['budget_cost'] += $resource->budget_cost;
                    $data[ $boq->type ]['budget_qty'] += $resource->budget_qty;
                }

            }
        }
        foreach ($data as $key => $value) {
            $data[ $key ]['dry_qty_eq'] = ($data[$key]['budget_cost'] - $data[$key]['dry_cost'])* $data[$key]['dry_qty'];
            $data[ $key ]['budget_cost_eq'] = ($data[$key]['budget_qty'] - $data[$key]['dry_qty'])* $data[$key]['budget_cost'];

            $total['dry_qty_eq'] +=$data[ $key ]['dry_qty_eq'];
            $total['budget_cost_eq'] +=$data[ $key ]['budget_cost_eq'];
        }
        return view('reports.qty_and_cost', compact('data','total','project'));
    }

}