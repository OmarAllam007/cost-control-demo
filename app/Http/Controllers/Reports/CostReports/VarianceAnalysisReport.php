<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 09/01/17
 * Time: 09:23 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;

class VarianceAnalysisReport
{

    function getVarianceReport(Project $project)
    {
        $data = '';
        $costShadows = CostShadow::joinBudget('budget.resource_type_id')
            ->sumFields([
                'cost.to_date_unit_price',
                'cost.unit_price_var',
                'cost.cost_var',
                'cost.allowable_qty',
                'cost.qty_var',
                'cost.cost_variance_completion_due_qty',
                'cost.cost_variance_completion_due_unit_price',
            'cost.to_date_qty'])
            ->where('period_id', $project->open_period()->id)
            ->get()->toArray();

        foreach ($costShadows as $costShadow) {
            if (!isset($data[$costShadow['resource_type_id']])) {
                $data[$costShadow['resource_type_id']] = $costShadow;
                $data[$costShadow['resource_type_id']]['unit_price'] = BreakDownResourceShadow::where('project_id', $project->id)->where('resource_type_id', $costShadow['resource_type_id'])->sum
                ('boq_equivilant_rate');
            }
            /* get cost accounts under resource_types **/
            $cost_accounts = CostShadow::joinBudget('budget.cost_account')
                ->sumFields([
                    'cost.to_date_unit_price',
                    'cost.unit_price_var',
                    'cost.cost_var',
                    'cost.allowable_qty',
                    'cost.qty_var',
                    'cost.cost_variance_completion_due_qty',
                    'cost.cost_variance_completion_due_unit_price',
                'cost.to_date_qty'])
                ->where('period_id', $project->open_period()->id)
                ->get()->toArray();

            foreach ($cost_accounts as $cost_account) {
                if (!isset($data[$costShadow['resource_type_id']]['cost_accounts'][$cost_account['cost_account']])) {
                    $data[$costShadow['resource_type_id']]['cost_accounts'][$cost_account['cost_account']] = $cost_account;
                    $data[$costShadow['resource_type_id']]['cost_accounts'][$cost_account['cost_account']]['unit_price'] = BreakDownResourceShadow::where('project_id', $project->id)->where('cost_account', $cost_account['cost_account'])->sum
                    ('boq_equivilant_rate');
                }

                /* get resource_name under cost_account **/

                $resources_names = CostShadow::joinBudget('budget.resource_id')
                    ->sumFields([
                        'cost.to_date_unit_price',
                        'cost.unit_price_var',
                        'cost.cost_var',
                        'cost.allowable_qty',
                        'cost.qty_var',
                        'cost.cost_variance_completion_due_qty',
                        'cost.cost_variance_completion_due_unit_price','cost.to_date_qty'])
                    ->where('period_id', $project->open_period()->id)
                    ->get()->toArray();

                foreach ($resources_names as $resource_name) {
                    if (!isset($data[$costShadow['resource_type_id']]['cost_accounts'][$cost_account['cost_account']]['resources'][$resource_name['resource_id']])) {
                        $data[$costShadow['resource_type_id']]['cost_accounts'][$cost_account['cost_account']]['resources'][$resource_name['resource_id']] = $resource_name;
                        $data[$costShadow['resource_type_id']]['cost_accounts'][$cost_account['cost_account']]['resources'][$resource_name['resource_id']]['unit_price'] =
                            BreakDownResourceShadow::where('project_id', $project->id)->where('resource_id', $resource_name['resource_id'])->sum
                            ('boq_equivilant_rate');
                    }
                }
            }
        }

        return view('reports.cost-control.variance_analysis.variance_analysis', compact('data','project'));
    }

}