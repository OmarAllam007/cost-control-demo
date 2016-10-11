<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 8:25 AM
 */

namespace App\Http\Controllers\Reports;


use App\Project;

class BudgetCostByBuilding
{
    public function getBudgetCostForBuilding(Project $project)
    {
        $breakdowns = $project->breakdowns()->get();
        $data  = [];
        $total =[
            'total'=>0,
            'weight'=>0,
        ];
        foreach ($breakdowns as $breakdown){
            $wbs_id  = $breakdown->wbs_level->id;
            if(!isset($data[$wbs_id])){
                $data[$wbs_id] = [
                    'name'=>$breakdown->wbs_level->name,
                    'code'=>$breakdown->wbs_level->code,
                    'budget_cost'=>0,
                    'weight'=>0,
                ];

            }
            foreach ($breakdown->resources as $resource){
                $data[$wbs_id]['budget_cost'] += $resource->budget_cost;
            }
        }
        foreach ($data as $item){
            $total['total'] +=$item['budget_cost'];
        }
        foreach ($data as $key=>$value){
            $data[$key]['weight'] = floatval(($data[$key]['budget_cost'] / $total['total']) * 100);
            $total['weight'] += $data[$key]['weight'];
        }

        return view('reports.budget_cost_by_building',compact('data','total'));
    }
}