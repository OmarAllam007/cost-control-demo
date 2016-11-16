<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 10:41 AM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Breakdown;
use App\BreakdownResource;
use App\Project;
use App\Survey;

class QtyAndCost
{
    public function compare(Project $project)
    {
//        $break_down_resources = $project->breakdown_resources()->get();
        $data = [];
        $total = [
            'budget_qty_eq' => 0,
            'budget_cost_eq' => 0,
        ];

        $breakdowns = $project->breakdowns()->get();
        foreach ($breakdowns as $breakdown) {
            $discipline = $breakdown->std_activity->discipline;
            $dry = Boq::where('cost_account', $breakdown->cost_account)->first();
            $budget_quantity = Survey::where('cost_account', $breakdown->cost_account)->first();
            $wbs = $breakdown->wbs_level;
            $cost_account = $breakdown->cost_account;
            if (!isset($data[ $discipline ])) {
                $data[ $discipline ] = [
                    'code' => $breakdown->std_activity->code,
                    'name' => $discipline,
                    'total_dry_cost' => 0,
                    'total_dry_qty' => 0,
                    'cost_accounts' => [],
                    'total_budget_qty_eq' => 0,
                    'total_budget_cost_eq' => 0,
                ];

            }


            if (!isset($data[ $discipline ]['cost_accounts'][ $cost_account ])) {
                $data[ $discipline ]['cost_accounts'][ $cost_account ] = [
                    'total_boq_equavalant_rate' => 0,
                    'dry_qty' => $dry->quantity,
                    'dry_cost' => $dry->dry_ur,
                    'wbs_levels' => [],
                    'account_budget_cost' => 0,
                    'account_budget_qty' => 0,
                    'budget_cost_eq' => 0,
                    'budget_qty_eq' => 0,
                ];
            }
            if (!isset($data[ $discipline ]['cost_accounts'][ $cost_account ]['wbs_levels'][ $wbs->name ])) {
                $data[ $discipline ]['cost_accounts'][ $cost_account ]['wbs_levels'][ $wbs->name ] = [
                    'budget_cost' => 0,
                    'budget_qty' => 0,
                ];
            }
            foreach ($breakdown->resources as $resource) {
                $data[ $discipline ]['cost_accounts'][ $cost_account ]['wbs_levels'][ $wbs->name ]['budget_cost'] += is_nan($resource->boq_unit_rate) ? 0 : $resource->budget_cost;

                $data[ $discipline ]['cost_accounts'][ $cost_account ]['wbs_levels'][ $wbs->name ]['budget_qty'] = is_nan($budget_quantity->budget_qty) ? 0 : $budget_quantity->budget_qty;
            }

        }

        foreach ($data as $key => $value) {
            foreach ($value['cost_accounts'] as $accountKey => $cost_account) {
                foreach ($cost_account['wbs_levels'] as $levelKey => $level) {
                    if ($level['budget_qty'] != 0) {

                        $data[ $key ]['cost_accounts'][ $accountKey ]['account_budget_cost'] += $level['budget_cost'];

                        $data[ $key ]['cost_accounts'][ $accountKey ]['account_budget_qty'] += $level['budget_qty'];

                    }

                }
                if ($data[ $key ]['cost_accounts'][ $accountKey ]['account_budget_qty']) {

                    $data[ $key ]['cost_accounts'][ $accountKey ]['total_boq_equavalant_rate'] += ($data[ $key ]['cost_accounts'][ $accountKey ]['account_budget_cost'] / $data[ $key ]['cost_accounts'][ $accountKey ]['account_budget_qty']);

                }

                //first_equation
                $data[ $key ]['cost_accounts'][ $accountKey ]['budget_cost_eq'] =
                    ($data[ $key ]['cost_accounts'][ $accountKey ]['total_boq_equavalant_rate'] - $data[ $key ]['cost_accounts'][ $accountKey ]['dry_cost']) * $data[ $key ]['cost_accounts'][ $accountKey ]['dry_qty'];

                //second equation
                $data[ $key ]['cost_accounts'][ $accountKey ]['budget_qty_eq'] =
                    ($data[ $key ]['cost_accounts'][ $accountKey ]['account_budget_qty'] - $data[ $key ]['cost_accounts'][ $accountKey ]['dry_qty']) * $data[ $key ]['cost_accounts'][ $accountKey ]['total_boq_equavalant_rate'];

                $data[ $key ]['total_budget_cost_eq'] += $data[ $key ]['cost_accounts'][ $accountKey ]['budget_cost_eq'];

                $data[ $key ]['total_budget_qty_eq'] += $data[ $key ]['cost_accounts'][ $accountKey ]['budget_qty_eq'];
            }
        }
        foreach ($data as $key => $value) {
            $total['budget_qty_eq'] += $data[ $key ]['total_budget_qty_eq'];
            $total['budget_cost_eq'] += $data[ $key ]['total_budget_cost_eq'];

        }
        return view('reports.qty_and_cost', compact('data', 'total', 'project'));
    }

}