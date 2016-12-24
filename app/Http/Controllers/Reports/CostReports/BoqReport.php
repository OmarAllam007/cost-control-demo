<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 24/12/16
 * Time: 07:39 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\CostShadow;
use App\Project;

class BoqReport
{
    function getReport(Project $project)
    {
        $shadows = CostShadow::joinBudget('budget.cost_account')
            ->sumFields([
                'cost.to_date_cost',
                'cost.previous_cost',
                'cost.allowable_ev_cost',
                'cost.remaining_cost',
                'cost.completion_cost',
                'cost.cost_var'])
            ->where('period_id', $project->open_period()->id)
            ->get()->toArray();
        dd($shadows);
    }
}