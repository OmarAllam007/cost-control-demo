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
use App\BreakDownResourceShadow;
use App\Project;
use App\WbsLevel;

class BudgetCostByBuilding
{
    private $total_budget;
    private $data = [];

    public function getBudgetCostForBuilding(Project $project)
    {
        $this->total_budget = BreakDownResourceShadow::where('project_id', $project->id)->sum('budget_cost');
        $wbs_levels = $project->wbs_tree;
        $tree = [];
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildReport($level);
            $tree [] = $treeLevel;
        }
        $this->getBudgetCostForBuildingPieChart($this->data);
        $this->getBugetCostByBuildingColumnChart($this->data);
        $total_budget = $this->total_budget;
        $data = $this->data;
        return view('reports.budget.budget_cost_by_building.budget_cost_by_building', compact('project', 'tree', 'total_budget','data'));
    }

    private function buildReport($level)
    {
        $tree = ['id' => $level->id, 'code' => $level->code, 'name' => $level->name, 'children' => [], 'budget_cost' => 0, 'weight' => 0];

        if ($level->children && $level->children->count()) {
            $tree['children'] = $level->children->map(function (WbsLevel $childLevel) {
                return $this->buildReport($childLevel);
            });
        }
        /** @var WbsLevel $level */
            $tree['budget_cost'] = BreakDownResourceShadow::whereIn('wbs_id', $level->getChildrenIds())->sum('budget_cost');
            $tree['weight'] = floatval(($tree['budget_cost'] / $this->total_budget) * 100);
        if ($level->getDry())
        {
            $this->data[$level->id] = ['name' => $level->name, 'weight' => $tree['weight'], 'budget_cost' => $tree['budget_cost']];
        }
        return $tree;
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