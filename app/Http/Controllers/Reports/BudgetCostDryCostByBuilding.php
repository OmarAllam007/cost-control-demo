<?php
namespace App\Http\Controllers\Reports;

use App\Boq;
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
        $total = [
            'total_dry' => 0,
            'total_budget' => 0,
            'total_increase' => 0,
            'difference' => 0,
        ];
        foreach ($break_downs as $break_down) {
            $wbs_id = $break_down->wbs_level->id;
            if (!isset($data[ $wbs_id ])) {
                $data[ $wbs_id ] = [
                    'name' => $break_down->wbs_level->name,
                    'code' => $break_down->wbs_level->code,
                    'dry_cost' => 0,
                    'budget_cost' => 0,
                    'difference' => 0,
                    'increase' => 0,
                ];
            }

            $data[ $wbs_id ]['dry_cost'] += Boq::where('cost_account', $break_down->cost_account)->sum(\DB::raw('dry_ur * quantity'));
            foreach ($break_down->resources as $resource) {
                $data[ $wbs_id ]['budget_cost'] += $resource->budget_cost;
            }

            $data[ $wbs_id ]['difference'] = ($data[ $wbs_id ]['budget_cost'] - $data[ $wbs_id ]['dry_cost']);

            if ($data[ $wbs_id ]['dry_cost']) {
                $data[ $wbs_id ]['increase'] = floatval(($data[ $wbs_id ]['budget_cost'] - $data[ $wbs_id ]['dry_cost']) / $data[ $wbs_id ]['dry_cost'] * 100);
            }

        }
        foreach ($data as $item) {
            $total['total_dry'] += $item['dry_cost'];
            $total['total_budget'] += $item['budget_cost'];
            $total['total_increase'] += $item['increase'];
            $total['difference'] += $item['difference'];
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