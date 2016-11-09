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
use Khill\Lavacharts\Lavacharts;

class BudgetCostByBuilding
{
    public function getBudgetCostForBuilding(Project $project)
    {
        $resources = $project->breakdown_resources()->get();
        $data = [];
        $total = [
            'total' => 0,
            'weight' => 0,
        ];
        foreach ($resources as $resource) {

            $wbs_level = $resource->breakdown->wbs_level;
            $boq = Boq::where('wbs_id', $wbs_level->id)->first();

            if ($boq->dry_ur) {
                if (!isset($data[ $wbs_level->id ])) {
                    $data[ $wbs_level->id ] = [
                        'name' => $resource->breakdown->wbs_level->name,
                        'code' => $resource->breakdown->wbs_level->code,
                        'budget_cost' => 0,
                        'weight' => 0,
                    ];

                }
                $data[ $wbs_level->id ]['budget_cost'] += $resource->budget_cost;
            } else {
                if($wbs_level->parent){
                    $parent = $wbs_level->parent;
                    while ($parent->parent) {
                        if (!isset($data[ $parent->id ])) {
                            $data[ $parent->id ] = [
                                'name' => $resource->breakdown->wbs_level->name,
                                'code' => $resource->breakdown->wbs_level->code,
                                'budget_cost' => 0,
                                'weight' => 0,
                            ];
                        }
                        $parent_dry = Boq::where('wbs_id', $parent->id)->first();
                        if (isset($parent_dry->dry_ur)) {
                            $parent_break = $resource->breakdown->where('wbs_level_id', $parent->id)->first();
                            if ($parent_break) {
                                $parent_resources = $parent_break->resources;
                                foreach ($parent_resources as $parent_resource) {
                                    $data[ $parent->id ]['budget_cost'] += $parent_resource->budget_cost;
                                }
                            }
                        }
                        $parent = $parent->parent;
                    }
                }

            }

        }

        foreach ($data as $item) {
            $total['total'] += $item['budget_cost'];
        }
        foreach ($data as $key => $value) {
            if ($total['total'] != 0) {
                $data[ $key ]['weight'] = floatval(($data[ $key ]['budget_cost'] / $total['total']) * 100);
                $total['weight'] += $data[ $key ]['weight'];
            }
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