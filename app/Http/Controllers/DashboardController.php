<?php

namespace App\Http\Controllers;

use App\BreakDownResourceShadow;
use App\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /** @var Collection */
    private $projects;

    function index()
    {
        $this->projects = Project::all();

        $contracts_info = $this->contracts_info();
        $finish_dates = $this->finish_dates();

        return view('dashboard.index', compact('contracts_info', 'finish_dates'));
    }

    private function contracts_info()
    {
        $contracts_total = $this->projects->sum('project_contract_signed_value');
        $change_orders = $this->projects->sum('change_order_amount');
        $revised = $contracts_total + $change_orders;
        $budget_total = BreakDownResourceShadow::sum('budget_cost');

        $profit = $budget_total - $contracts_total - $change_orders;
        $profitability = $profit * 100 / $contracts_total;
        $finish_date = Carbon::parse($this->projects->max('expected_finished_date'));

        return compact('contracts_total', 'change_orders', 'revised', 'profit', 'profitability', 'finish_date');
    }


    private function finish_dates()
    {
        return $this->projects->filter(function($project) {
            return $project->project_start_date != '0000-00-00' && $project->expected_finished_date != '0000-00-00';
        })->map(function ($project) {
            return [
                'title' => $project->project_code,
                'start_date' => Carbon::parse($project->project_start_date)->format('d M Y'),
                'finish_date' => Carbon::parse($project->expected_finished_date)->format('d M Y'),
            ];
        });
    }






    function dashboard()
    {
        $projectNames = Project::orderBy('name')->pluck('name', 'id');
        $projectStats = collect(\DB::select('SELECT sh.project_id, sum(budget_cost) AS budget_cost, sum(cost) AS actual_cost FROM break_down_resource_shadows sh LEFT JOIN actual_resources ar ON (sh.breakdown_resource_id = ar.breakdown_resource_id) GROUP BY sh.project_id'))
            ->keyBy('project_id')->map(function ($project) {
                return ['budget_cost' => $project->budget_cost ?: 0, 'actual_cost' => $project->actual_cost ?: 0];
            });

        $topActivities = \DB::select('SELECT activity, sum(budget_cost) AS budget_cost, sum(cost) AS actual_cost FROM break_down_resource_shadows sh LEFT JOIN actual_resources ar ON (sh.breakdown_resource_id = ar.breakdown_resource_id) GROUP BY activity ORDER BY 2 DESC, 1 LIMIT 10');
        $topResources = \DB::select('SELECT resource_name, sum(budget_cost) AS budget_cost, sum(cost) AS actual_cost FROM break_down_resource_shadows sh LEFT JOIN actual_resources ar ON (sh.breakdown_resource_id = ar.breakdown_resource_id) GROUP BY resource_name ORDER BY 2 DESC, 1 LIMIT 10');
        $resourceTypes = \DB::select('SELECT resource_type, sum(budget_cost) AS budget_cost, sum(cost) AS actual_cost FROM break_down_resource_shadows sh LEFT JOIN actual_resources ar ON (sh.breakdown_resource_id = ar.breakdown_resource_id) GROUP BY resource_type ORDER BY 1');

        return view('dashboard', compact('projectNames', 'projectStats', 'topActivities', 'topResources', 'resourceTypes'));
    }

}
