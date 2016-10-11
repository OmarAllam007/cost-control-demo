<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 4:24 PM
 */

namespace App\Http\Controllers\Reports;


use App\Project;
use App\Survey;

class BudgetCostByDiscipline
{
    public function compareBudgetCostDiscipline(Project $project)
    {
        $survey = [];
        $total = [
            'total'=>0,
            'weight_total'=>0
        ];
        $breakdowns = $project->breakdowns()->get();
        foreach ($breakdowns as $breakdown) {
            $qs_items = Survey::where('cost_account', $breakdown->cost_account)->get();
            foreach ($qs_items as $qs_item) {
                if (!isset($survey[ $qs_item->discipline ])) {
                    $survey[ $qs_item->discipline ] = [
                        'code' => $qs_item->code,
                        'name' => $qs_item->discipline,
                        'budget_cost' => 0,
                        'weight'=>0
                    ];
                }
                foreach ($breakdown->resources as $resource) {
                    $survey[ $qs_item->discipline ]['budget_cost'] += $resource->budget_cost;
                }


            }

        }
        foreach ($survey as $item){
            $total['total'] +=$item['budget_cost'];
        }
        foreach ($survey as $key=>$value){
            $survey[$key]['weight'] += floatval(($survey[$key]['budget_cost'] / $total['total']) * 100);
            $total['weight_total'] += $survey[$key]['weight'];
        }
        return view('reports.budget_cost_by_discipline', compact('project', 'survey','total'));

    }
}