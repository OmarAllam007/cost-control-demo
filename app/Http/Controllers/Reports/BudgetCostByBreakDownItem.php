<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 2:10 PM
 */

namespace App\Http\Controllers\Reports;

use App\Project;

class BudgetCostByBreakDownItem
{
    public function compareBudgetCostByBreakDownItem(Project $project)
    {
        $break_downs = $project->breakdowns()->get();

        $bd_resource = [];
        $total = [
            'total' => 0,
            'weight_total'=>0
        ];
        foreach ($break_downs as $break_down) {
            foreach ($break_down->resources as $resource) {
                $root = $resource->resource->resource->types->root;
                if (!isset($bd_resource[ $root->name ])) {
                    $bd_resource[ $root->name ] = [
                        'resource_type' => $root->name,
                        'resource_code' => $root->code,
                        'budget_cost' => 0,
                        'weight' => 0,

                    ];
                }
                $bd_resource[ $root->name ]['budget_cost'] += $resource->budget_cost;
            }


        }
        foreach ($bd_resource as $item) {
            $total['total'] += $item['budget_cost'];

        }
        foreach ($bd_resource as $key => $value) {
            $bd_resource[$key]['weight'] += floatval(($bd_resource[$key]['budget_cost'] / $total['total']) * 100);
            $total['weight_total'] += $bd_resource[$key]['weight'];
        }

        ksort($bd_resource);
        return view('reports.budget_cost_by_break_down',compact('bd_resource','total'));
    }
}