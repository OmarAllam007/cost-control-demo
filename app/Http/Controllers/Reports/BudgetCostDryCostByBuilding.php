<?php
namespace App\Http\Controllers\Reports;

use App\Boq;
use App\Breakdown;
use App\Project;

/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 12:27 PM
 */
class BudgetCostDryCostByBuilding
{
    public function compareBudgetCostDryCost($project)
    {
        $break_downs = $project->breakdowns()->get();
        $data = [];
        $children = [];
        $total = [
            'total_dry' => 0,
            'total_budget' => 0,
            'total_increase' => 0,
            'difference' => 0,
        ];
        $parents = [];

        foreach ($break_downs as $break_down) {
            $wbs_level = $break_down->wbs_level;
            $dry = $break_down->getDry($wbs_level->id);

            if ($dry) {//if wbs-level has dry
                if (!isset($data[ $wbs_level->id ])) {
                    $data[ $wbs_level->id ] = [
                        'name' => $break_down->wbs_level->name,
                        'code' => $break_down->wbs_level->code,
                        'dry_cost' => 0,
                        'budget_cost' => 0,
                        'difference' => 0,
                        'increase' => 0,
                    ];

                    $data[ $wbs_level->id ]['dry_cost'] += Boq::where('wbs_id', $wbs_level->id)->sum(\DB::raw('dry_ur * quantity'));
                }
                $resources = $break_down->resources;
                foreach ($resources as $resource) {
                    $data[ $wbs_level->id ]['budget_cost'] += is_nan($resource->budget_cost) ? 0 : $resource->budget_cost;
                }
            } else {//if wbs-level has not dry
                $parent = $wbs_level;
                while ($parent->parent) {//get budget cost of parent
                    $parent = $parent->parent;
                    $parent_dry = $break_down->getDry($parent->id);
                    if ($parent_dry) {
                        if (!isset($data[ $parent->id ])) {
                            $data[ $parent->id ] = [
                                'name' => $parent->name,
                                'code' => $parent->code,
                                'dry_cost' => 0,
                                'budget_cost' => $parent->budget_cost['budget_cost'],
                                'difference' => 0,
                                'increase' => 0,
                            ];
                            $children = $parent->budget_cost['children'];

                        }
                        foreach ($parent->children as $child){
                            $data[ $parent->id ]['dry_cost'] += Boq::where('wbs_id', $child->id)->sum(\DB::raw('dry_ur * quantity'));
                        }

                    }
                    break;

                }


                $data[ $parent->id ]['difference'] += ($data[ $parent->id ]['budget_cost'] - $data[ $parent->id ]['dry_cost']);

                if ($data[ $parent->id ]['dry_cost']) {
                    $data[ $parent->id ]['increase'] += floatval(($data[ $parent->id ]['budget_cost'] - $data[ $parent->id ]['dry_cost']) / $data[ $parent->id ]['dry_cost'] * 100);
                }
            }
        }

        foreach ($data as $key => $item) {
            if (in_array($key, $children)) {
                unset($data[ $key ]);
                continue;
            }
        }
        //delete parents from array if no dry exist
        foreach ($data as $key => $item) {

            $data[ $key ]['difference'] = $data[ $key ]['budget_cost'] - $data[ $key ]['dry_cost'];
            if ($data[ $key ]['dry_cost']) {
                $data[ $key ]['increase'] += floatval(($data[ $key ]['budget_cost'] - $data[ $key ]['dry_cost']) / $data[ $key ]['dry_cost'] * 100);
            }

            $total['total_dry'] += $data[ $key ]['dry_cost'];
            $total['total_budget'] += $data[ $key ]['budget_cost'];
            $total['total_increase'] += $data[ $key ]['increase'];
            $total['difference'] += $data[ $key ]['difference'];

        }

        if ($total['total_budget']) {
            $total['total_increase'] = $total['difference'] / $total['total_budget'] * 100;
        }

        $this->getBudgetCostDryCostColumnChart($data);
        $this->getBudgetCostDryCostSecondColumnChart($data);
        $this->getBudgetCostDryCostThirdColumnChart($data);
        return view('reports.budget_cost_dry_cost', compact('project', 'break_downs', 'data', 'total'));


    }

    public function getBudgetCostDryCostColumnChart($data)
    {
        $costTable = \Lava::DataTable();

        $costTable->addStringColumn('WBS')->addNumberColumn('Dry Cost')->addNumberColumn('Budget Cost');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[ $key ]['name'], $data[ $key ]['dry_cost'], $data[ $key ]['budget_cost']]);

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
            $costTable->addRow([$data[ $key ]['name'], $data[ $key ]['difference']]);

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

        $costTable->addStringColumn('WBS')->addNumberColumn('Increase')->addRoleColumn(
            'role', 'annotation');
        foreach ($data as $key => $value) {
            $costTable->addRow([$data[ $key ]['name'], number_format($data[ $key ]['increase'], 1), $data[ $key ]['name']]);
        }
        $options = [

            'tooltip' => 'percent',
            'titleTextStyle' => [
                'color' => '#eb6b2c',
                'fontSize' => 14,
                'width' => '1000',
                'height' => '600',
            ],
            'title' => trans('Increase %'),
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