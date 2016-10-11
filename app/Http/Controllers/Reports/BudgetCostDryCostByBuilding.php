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

            $data[ $wbs_id ]['increase'] = floatval(($data[ $wbs_id ]['budget_cost'] - $data[ $wbs_id ]['dry_cost']) / $data[ $wbs_id ]['dry_cost'] * 100);
        }
        foreach ($data as $item) {
            $total['total_dry'] += $item['dry_cost'];
            $total['total_budget'] += $item['budget_cost'];
            $total['total_increase'] += $item['increase'];
            $total['difference'] += $item['difference'];
        }
        $total['total_increase'] = $total['difference']/$total['total_budget']*100;

        return view('reports.budget_cost_dry_cost', compact('project', 'break_downs', 'data', 'total'));
    }
}