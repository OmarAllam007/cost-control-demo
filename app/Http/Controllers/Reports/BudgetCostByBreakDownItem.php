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
        $break_downs = $project->breakdown_resources()->get();

        $bd_resource = [];
        $total = [
            'total' => 0,
            'weight_total'=>0
        ];

            foreach ($break_downs as $resource) {
                $root = $resource->resource->types->root;
                if (!isset($bd_resource[ $root->name ])) {
                    $bd_resource[ $root->name ] = [
                        'resource_type' => $root->name,
                        'resource_code' => $root->code,
                        'budget_cost' => 0,
                        'weight' => 0,

                    ];
                }
                $bd_resource[ $root->name ]['budget_cost'] += is_nan($resource->budget_cost)?0:$resource->budget_cost;

        }
        foreach ($bd_resource as $item) {
            $total['total'] += $item['budget_cost'];

        }
        foreach ($bd_resource as $key => $value) {
            if($total['total']){
                $bd_resource[$key]['weight'] += floatval(($bd_resource[$key]['budget_cost'] / $total['total']) * 100);
                $total['weight_total'] += $bd_resource[$key]['weight'];
            }

        }

        ksort($bd_resource);
        $this->compareBudgetCostByBreakDownItemChart($bd_resource);
        return view('reports.budget_cost_by_break_down',compact('bd_resource','total','project'));
    }

     public function compareBudgetCostByBreakDownItemChart($data){
        $item = \Lava::DataTable();
        $item->addStringColumn('Resource Type')->addNumberColumn('Weight');
        foreach ($data as $key => $value) {
            $item->addRow([$data[ $key ]['resource_type'], $data[ $key ]['weight']]);
        }
        \Lava::PieChart('BreakDown', $item, [
            'width' => '1000',
            'height' => '600',
            'title' => 'Budget Cost | % Weight',
            'is3D' => true,
            'slices' => [
                ['offset' => 0.0],
                ['offset' => 0.0],
                ['offset' => 0.0],
            ],
            'pieSliceText' => "label",
            'sliceVisibilityThreshold'=>0,
        ]);

    }
}