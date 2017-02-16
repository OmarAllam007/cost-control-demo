<?php
namespace App\Http\Controllers\Reports;

use App\Boq;
use App\Breakdown;
use App\BreakDownResourceShadow;
use App\Project;
use App\WbsLevel;
use Barryvdh\Reflection\DocBlock\Type\Collection;

/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 12:27 PM
 */
class BudgetCostDryCostByBuilding
{

    private $total_budget;
    private $total_dry;
    private $total_increase;
    private $total_different;
    private $boqs;
    private $data = [];


    public function compareBudgetCostDryCost($project)
    {
        $this->boqs = collect();
        set_time_limit(300);
        $this->total_budget = BreakDownResourceShadow::where('project_id', $project->id)->sum('budget_cost');

        $localBoqs = \DB::table('boqs as b ')
            ->where('project_id', '=', $project->id)
            ->whereIn('b.cost_account', function ($query) {
                $query->select('cost_account')->from('break_down_resource_shadows');
            })
            ->groupBy('wbs_id')
            ->selectRaw('wbs_id,SUM(b.quantity * b.dry_ur) dry')->get();

        foreach ($localBoqs as $boq) {
            $this->boqs->put($boq->wbs_id, $boq->dry);
        }

        $wbs_levels = $project->wbs_tree;
        $tree = [];
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildReport($level);
            $tree [] = $treeLevel;
        }
        $this->total_dry += $this->boqs->sum();
//   $this->getBudgetCostForBuildingPieChart($this->data);
//   $this->getBugetCostByBuildingColumnChart($this->data);
        $total_budget = $this->total_budget;
        $total_difference = $this->total_different;
        $total_increase = $this->total_increase;
        $total_dry = $this->total_dry;
        $this->getBudgetCostDryCostColumnChart($this->data);
        $this->getBudgetCostDryCostSecondColumnChart($this->data);
        $this->getBudgetCostDryCostThirdColumnChart($this->data);
        return view('reports.budget.budget_cost_dry_building.budget_cost_dry_building', compact('project', 'tree', 'total_budget', 'total_dry', 'total_increase', 'total_difference'));
    }

    private function buildReport($level)
    {
        $tree = ['id' => $level->id, 'code' => $level->code, 'name' => $level->name, 'children' => [], 'budget_cost' => 0, 'dry_cost' => 0, 'different' => 0, 'increase' => 0];

        if ($level->children && $level->children->count()) {
            $tree['children'] = $level->children->map(function (WbsLevel $childLevel) use ($tree){
                return $this->buildReport($childLevel);
            });
        }

        /** @var WbsLevel $level */
        $tree['budget_cost'] = BreakDownResourceShadow::whereIn('wbs_id', $level->getChildrenIds())->sum('budget_cost');
        foreach ($level->getChildrenIds() as $id){
            $tree['dry_cost'] += $this->boqs->get($id) ??0;
        }
        if ($tree['dry_cost'] != 0) {
            $tree['different'] = $tree['budget_cost'] - $tree['dry_cost'];
            $tree['increase'] = ceil(floatval(($tree['different'] / $tree['dry_cost']) * 100));
            $this->total_different += $tree['different'];
            $this->total_increase += $tree['increase'];

        }
        if ($level->getDry()) {
            $this->data[$level->id] = ['name' => $level->name, 'budget_cost' => $tree['budget_cost'], 'dry_cost' => $tree['dry_cost'], 'different' => $tree['different'], 'increase' => $tree['increase']];

        }
        return $tree;
    }



    public function getBudgetCostDryCostColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('WBS')->addNumberColumn('Dry Cost')->addNumberColumn('Budget Cost');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[$key]['name'], $data[$key]['dry_cost'], $data[$key]['budget_cost']]);

        }
        $options = [
            'toolTip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Budget VS Dry'),
            'height' => 400,
            'hAxis' => [
                'title' => 'WBS',
            ],
            'vAxis' => [
                'title' => '',
            ],

        ];
        \Lava::ColumnChart('BudgetCost', $costTable, $options);

    }

    public function getBudgetCostDryCostSecondColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('WBS')->addNumberColumn('Difference');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[$key]['name'], $data[$key]['different']]);

        }
        $options = [
            'isStacked' => 'false',
            'tooltip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Difference'),
            'height' => 400,
            'hAxis' => [
                'title' => 'WBS',
            ],
            'vAxis' => [
                'title' => '',
            ],
            'labels' => [
                'visible' => 'true',
            ],
            'legend' => [
                'position' => 'none',
            ],
            'bar' => [
                'groupWidth' => '30%',
            ],
        ];
        \Lava::ColumnChart('Difference', $costTable, $options);

    }

    public function getBudgetCostDryCostThirdColumnChart($data)
    {

        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('WBS')->addNumberColumn('Increase');
        foreach ($data as $key => $value) {

            $costTable->addRow([$data[$key]['name'], $value['increase']]);
        }
        $options = [
            'isStacked' => 'false',
            'tooltip' => 'value',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Increase'),
            'height' => 400,
            'hAxis' => [
                'title' => 'WBS',
            ],
            'vAxis' => [
                'title' => '',
            ],
            'labels' => [
                'visible' => 'true',
            ],
            'legend' => [
                'position' => 'none',
            ],
            'bar' => [
                'groupWidth' => '30%',
            ],
        ];
        \Lava::ColumnChart('Increase', $costTable, $options);

    }
}