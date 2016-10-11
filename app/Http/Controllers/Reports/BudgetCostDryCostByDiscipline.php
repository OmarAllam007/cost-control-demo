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
        $break_downs = $project->breakdowns()->get();
        $data = [];
        $total = [
            'dry_cost'=>0,
            'budget_cost'=>0,
            'difference'=>0,
            'increase'=>0,
        ];
        foreach ($break_downs as $break_down) {
            $boqs = Boq::where('cost_account', $break_down->cost_account);
            foreach ($boqs->get() as $boq) {
                if (!isset($data[ $boq->type ])) {
                    $data[ $boq->type ] = [
                        'code'=>$boq->item_code,
                        'name' => $boq->type,
                        'dry_cost'=>0,
                        'budget_cost'=>0,
                        'difference'=>0,
                        'increase'=>0,
                    ];
                }
                $data[ $boq->type ]['dry_cost'] += $boqs->sum(\DB::raw('dry_ur * quantity'));

                foreach ($break_down->resources as $resource) {
                    $data[ $boq->type ]['budget_cost'] += $resource->budget_cost;
                }

            }
        }
        foreach ($data as $key=>$value){
            $data[$key]['difference'] += ($data[$key]['budget_cost']-$data[$key]['dry_cost']);
            $data[$key]['increase']+=  ($data[$key]['difference']/$data[$key]['dry_cost']);
            $total['difference'] +=$data[$key]['difference'];
            $total['dry_cost'] +=$data[$key]['dry_cost'];
            $total['budget_cost'] +=$data[$key]['budget_cost'];
            $total['increase'] +=$data[$key]['increase'];
        }
        return view('reports.budget_cost_dry_cost_by_discipline',compact('data','total'));
    }

}