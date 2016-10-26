<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 8:25 AM
 */

namespace App\Http\Controllers\Reports;


use App\Project;
use Khill\Lavacharts\Lavacharts;

class BudgetCostByBuilding
{
    public function getBudgetCostForBuilding(Project $project)
    {
        $breakdowns = $project->breakdowns()->get();
        $data = [];
        $total = [
            'total' => 0,
            'weight' => 0,
        ];
        foreach ($breakdowns as $breakdown) {
            $wbs_id = $breakdown->wbs_level->id;
            if (!isset($data[ $wbs_id ])) {
                $data[ $wbs_id ] = [
                    'name' => $breakdown->wbs_level->name,
                    'code' => $breakdown->wbs_level->code,
                    'budget_cost' => 0,
                    'weight' => 0,
                ];

            }
            foreach ($breakdown->resources as $resource) {
                $data[ $wbs_id ]['budget_cost'] += $resource->budget_cost;
            }
        }
        foreach ($data as $item) {
            $total['total'] += $item['budget_cost'];
        }
        foreach ($data as $key => $value) {
            $data[ $key ]['weight'] = floatval(($data[ $key ]['budget_cost'] / $total['total']) * 100);
            $total['weight'] += $data[ $key ]['weight'];
        }
        $pieChart = $this->getBudgetCostForBuildingPieChart($data);
        $columnChart = $this->getBugetCostByBuildingColumnChart($data);
        return view('reports.budget_cost_by_building', compact('data', 'total', 'project', 'pieChart', 'columnChart'));
    }

    public function getBudgetCostForBuildingPieChart($data)
    {
        $building = \Lava::DataTable();
        $building->addStringColumn('Buildings')->addNumberColumn('Weight');
        foreach ($data as $key => $value) {
            $building->addRow([$data[ $key ]['name'], $data[ $key ]['weight']]);
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
            'pieSliceText' => "value",
        ]);

    }

    public function getBugetCostByBuildingColumnChart($data)
    {
//        $columnChart = new Lavacharts; // See note below for Laravel
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('BudgetCost')->addNumberColumn('WBS');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[ $key ]['name'], $data[ $key ]['budget_cost']]);

        }
        \Lava::ColumnChart('BudgetCost', $costTable, [
            'title' => 'Budget Cost',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width'=>'1000',
                'height'=>'600',
            ],
        ]);

    }
}