<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 8:25 AM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;

use App\Breakdown;
use App\BreakdownResource;
use App\Project;

class BudgetCostByBuilding
{
    public function getBudgetCostForBuilding(Project $project)
    {
        set_time_limit(200);
        $shadows = $project->shadows()->with('wbs')->get();
        $data = [];
//        $children = [];
        $total = [
            'total' => 0,
            'weight' => 0,
        ];
        $children=[];

        foreach ($shadows as $shadow) {
            $wbs_level = $shadow->wbs;
            $name = $wbs_level->name;
            $dry = $shadow->breakdown->getDry($project,$wbs_level->id,$shadow['cost_account']);
            if ($dry) {
                if (!isset($data[$name])) {
                    $data[$name] = [
                        'name' => $name,
                        'code' => $wbs_level->code,
                        'budget_cost' => 0,
                        'weight' => 0,
                    ];

                }
                $data[$name]['budget_cost'] += is_nan($shadow['budget_cost']) ? 0 : $shadow['budget_cost'];

            } else {
                $parent = $wbs_level;
                while ($parent->parent) {
                    $parent = $parent->parent;
                    $parent_name = $parent->name;
                    $parent_dry = $shadow->breakdown->getDry($project,$parent->id,$shadow['cost_account']);
                    if ($parent_dry) {
                        if (!isset($data[$parent_name])) {
                            $data[$parent_name] = [
                                'name' => $parent_name,
                                'code' => $parent->code,
                                'budget_cost' => $parent->budget_cost['budget_cost'],
                                'weight' => 0,
                            ];
                            $children[] = $parent->budget_cost['children'];
                        }
                        break;
                    }

                }
            }


        }

        foreach ($data as $key => $item) {//fill total array
            if (in_array($key, $children)) {
                unset($data[$key]);
                continue;
            }
            $total['total'] += $item['budget_cost'];
        }
        foreach ($data as $key => $value) {
            if (in_array($key, $children)) {
                continue;
            }
            if ($total['total'] != 0) {
                $data[$key]['weight'] = floatval(($data[$key]['budget_cost'] / $total['total']) * 100);
                $total['weight'] += $data[$key]['weight'];
            }
        }

//        ksort($data);
        $pieChart = $this->getBudgetCostForBuildingPieChart($data);
        $columnChart = $this->getBugetCostByBuildingColumnChart($data);
        return view('reports.budget_cost_by_building', compact('data', 'total', 'project', 'pieChart', 'columnChart'));
    }

    public function getBudgetCostForBuildingPieChart($data)
    {
        $building = \Lava::DataTable();
        $building->addStringColumn('Buildings')->addNumberColumn('Weight');
        foreach ($data as $key => $value) {
            $building->addRow([$data[$key]['name'], $data[$key]['weight']]);
        }
        \Lava::PieChart('BOQ', $building, [
            'width' => '1000',
            'height' => '600',
            'title' => 'Budget Cost | % Weight',
            'is3D' => true,
            'slices' => [
                ['offset' => 0.0],
                ['offset' => 0.0],
                ['offset' => 0.0],
            ],
            'pieSliceText' => "precentage",
        ]);

    }

    public function getBugetCostByBuildingColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('BudgetCost')->addNumberColumn('WBS');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[$key]['name'], $data[$key]['budget_cost']]);

        }
        \Lava::ColumnChart('BudgetCost', $costTable, [
            'title' => 'Budget Cost',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
        ]);

    }
}