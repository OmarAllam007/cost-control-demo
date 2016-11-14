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
use App\Project;

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
        $parents = [];

        foreach ($breakdowns as $breakdown) {
            $wbs_level = $breakdown->wbs_level;
            $dry = $breakdown->getDry($wbs_level->id);
            $resources = $breakdown->resources;
            if ($dry) {
                    if (!isset($data[ $wbs_level->id ])) {
                        $data[ $wbs_level->id ] = [
                            'name' => $wbs_level->name,
                            'code' => $wbs_level->code,
                            'dry' => '',
                            'budget_cost' => 0,
                            'weight' => 0,
                        ];

                    }
                foreach ($resources as $resource) {
                    $data[ $wbs_level->id ]['budget_cost'] += is_nan($resource->budget_cost)?0:$resource->budget_cost;
                }



            } else {
                $parent = $wbs_level;
                while ($parent->parent)
                {
                    $parent = $parent->parent;
                    if (!isset($data[ $wbs_level->id ])) {
                        $data[ $wbs_level->id ] = [
                            'name' => $wbs_level->name,
                            'code' => $wbs_level->code,
                            'dry' => ' (Dry = 0)',
                            'budget_cost' => 0,
                            'weight' => 0,
                        ];
                        $parent_break_down = Breakdown::where('wbs_level_id', $parent->id)->first();
                        if ($parent_break_down) {
                            $parent_resources = $parent_break_down->resources;
                            foreach ($parent_resources as $parent_resource) {
                                $data[ $wbs_level->id ]['budget_cost'] += is_nan($parent_resource->budget_cost)?0:$parent_resource->budget_cost;
                            }
                        }
                        $parents [$parent->id] = $parent->id; }

                }
            }
        } //iterate over breakdowns
        foreach ($parents as $key=>$value){
            unset($data[$key]);
        } //unset parents

        foreach ($data as $key => $item) {//fill total array
            $total['total'] += $item['budget_cost'];
        }
        foreach ($data as $key => $value) {
            if ($total['total'] != 0) {
                $data[ $key ]['weight'] = floatval(($data[ $key ]['budget_cost'] / $total['total']) * 100);
                $total['weight'] += $data[ $key ]['weight'];
            }
        }
        $pieChart = $this->getBudgetCostForBuildingPieChart($data); $columnChart = $this->getBugetCostByBuildingColumnChart($data); return view('reports.budget_cost_by_building', compact('data', 'total', 'project', 'pieChart', 'columnChart'));
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
            'pieSliceText' => "precentage",
        ]);

    }

    public function getBugetCostByBuildingColumnChart($data)
    {
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
                'width' => '1000',
                'height' => '600',
            ],
        ]);

    }
}