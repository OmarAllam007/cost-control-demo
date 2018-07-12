<?php

namespace App\Reports\Cost;

use App\BudgetRevision;
use App\GlobalPeriod;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\StdActivity;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class GlobalReport
{
    /** @var Collection */
    protected $cpi_trend;

    /** @var Collection */
    protected $waste_index_trend = null;

    /** @var Collection */
    private $projects;

    /** @var Collection */
    private $last_period_ids;

    /** @var GlobalPeriod */
    private $period;

    /** @var Collection */
    private $periods;

    /** @var Collection */
    private $trend_period_ids;

    /** @var Collection */
    private $trend_global_periods;

    /** @var Collection */
    private $cost_summary;
    /** @var Collection */
    private $projects_summary;

    function __construct(GlobalPeriod $period)
    {
        $this->period = $period;
    }

    function run()
    {
        $key = 'global-report-' . $this->period->id;

        if (request()->exists('clear') || request()->exists('refresh')) {
            \Cache::forget($key);
        }

        return \Cache::remember($key, Carbon::tomorrow(), function () {
            return $this->getInfo();
        });
    }

    function getInfo()
    {
        $this->last_period_ids = Period::readyForReporting()
            ->selectRaw('max(id) as id, project_id')
            ->where('global_period_id', $this->period->id)
            ->groupBy('project_id')
            ->pluck('id');

        $this->periods = Period::with('project')->find($this->last_period_ids->toArray());
        $this->projects = $this->periods->pluck('project');

        $this->trend_global_periods = GlobalPeriod::latest('end_date')->take(6)->where('end_date', '>=', '2017-10-01')->get()->keyBy('id');
        $this->trend_period_ids = Period::whereIn('global_period_id', $this->trend_global_periods->pluck('id'))
            ->whereIn('project_id', $this->projects->pluck('id'))
            ->selectRaw('max(id) as id, project_id, global_period_id')
            ->groupBy(['project_id', 'global_period_id'])->pluck('id');

        return [
            'revenue_statement' => $this->revenue_statement(),
            'cost_summary' => $this->cost_summary(),
            'cost_info' => $this->cost_info(),
            'period' => $this->period,
            'projectNames' => $this->projects->pluck('name', 'id'),
            'contracts_info' => $this->contracts_info(),
            'finish_dates' => $this->finish_dates(),
            'budget_info' => $this->budget_info(),
            'cost_percentage_chart' => $this->cost_percentage(),
            'cpi_trend' => $this->cpi_trend(),
            'spi_trend' => $this->spi_trend(),
            'waste_index_trend' => $this->waste_index_trend(),
            'pi_trend' => $this->productivity_index_trend(),
            'completionValues' => $this->completionValues
        ];
    }

    private function contracts_info()
    {
        $contracts_total = $this->projects->sum('project_contract_signed_value');
        $change_orders = $this->periods->sum('change_order_amount');
        $potential_change_orders = $this->periods->sum('potential_change_order_amount');

        $revised = $contracts_total + $change_orders;
//        $budget_total = BreakDownResourceShadow::sum('budget_cost');

        $profit = $this->projects->sum('tender_initial_profit');
        $profitability = $profit * 100 / $revised;
        $finish_date = Carbon::parse($this->periods->max('forecast_finish_date'));

        $schedules = $this->periods->map(function ($period) {
            $schedule = new Fluent();
            $schedule->project_name = $period->project->name;
            $schedule->planned_start = $period->project->project_start_date? Carbon::parse($period->project->project_start_date)->format('d M Y') : '';
            $schedule->original_duration = $period->expected_duration;
            $schedule->planned_finish = $period->planned_finish_date? Carbon::parse($period->planned_finish_date)->format('d M Y') : '';
            $schedule->actual_start = $period->project->actual_start_date? Carbon::parse($period->project->actual_start_date)->format('d M Y') : '';
            $schedule->expected_duration = $period->actual_duration;
            $schedule->forecast_finish = $period->forecast_finish_date ? Carbon::parse($period->forecast_finish_date)->format('d M Y') : '';
            $schedule->delay_variance = $period->duration_variance;
            $schedule->planned_progress = $period->planned_progress;
            $schedule->actual_progress = $period->actual_progress;
            $schedule->spi_index = $period->spi_index;
            $schedule->allowable_cost = $period->allowable_cost_for_reports;
            $schedule->to_date_cost = $period->to_date_cost_for_reports;
            $schedule->variance = $schedule->allowable_cost - $schedule->to_date_cost;
            $schedule->cpi = 0;
            if ($schedule->to_date_cost) {
                $schedule->cpi = $schedule->allowable_cost / $schedule->to_date_cost;
            }

            return $schedule;
        })->sortBy('project_name');

        return compact('contracts_total', 'change_orders', 'potential_change_orders', 'revised', 'profit', 'profitability', 'finish_date', 'schedules');
    }


    private function finish_dates()
    {
        return $this->projects->filter(function ($project) {
            return $project->project_start_date != '0000-00-00' && $project->expected_finish_date != '0000-00-00';
        })->map(function ($project) {
            return [
                'title' => $project->project_code,
                'start_date' => Carbon::parse($project->project_start_date)->format('d M Y'),
                'finish_date' => Carbon::parse($project->expected_finish_date)->format('d M Y'),
            ];
        });
    }

    private function budget_info()
    {
        $firstRevisions = $this->projects->map(function ($project) {
            $revision = BudgetRevision::where('project_id', $project->id)->oldest()->first();

            if (!$revision) {
                $revision = $project;
            }

            $project_id = $project->id;
            $eac_contract_amount = $revision->eac_contract_amount;
            $budget_cost = $revision->budget_cost;
            $general_requirements = $revision->general_requirement_cost;
            $management_reserve = $revision->management_reserve_cost;

            return new Fluent(compact('project_id', 'eac_contract_amount', 'budget_cost', 'general_requirements', 'management_reserve'));
        });

        $budget_cost = $firstRevisions->sum('budget_cost');
        $eac_contracts_value = $firstRevisions->sum('eac_contract_amount');
        $general_requirements = $firstRevisions->sum('general_requirements');
        $management_reserve = $firstRevisions->sum('management_reserve');
        $profit = $eac_contracts_value - $budget_cost;
        $revision0 = [
            'budget_cost' => $budget_cost,
            'direct_cost' => $budget_cost - $general_requirements - $management_reserve,
            'indirect_cost' => $general_requirements + $management_reserve,
            'eac_contracts_value' => $eac_contracts_value,
            'profit' => $profit,
            'profitability' => $profit * 100 / $eac_contracts_value,
        ];

        $latestRevisions = $this->projects->map(function ($project) {
            $revision = BudgetRevision::where('project_id', $project->id)
                ->where('global_period_id', '<=', $this->period->id)->latest()->first();

            if (!$revision) {
                $revision = $project;
            }

            $eac_contract_amount = $revision->eac_contract_amount;
            $budget_cost = $revision->budget_cost;
            $general_requirements = $revision->general_requirement_cost;
            $management_reserve = $revision->management_reserve_cost;
            $project_id = $project->id;

            return new Fluent(compact('project_id', 'eac_contract_amount', 'budget_cost', 'general_requirements', 'management_reserve'));
        });

        $budget_cost = $latestRevisions->sum('budget_cost');
        $eac_contracts_value = $latestRevisions->pluck('eac_contract_amount', 'project_id')->sum();
        $general_requirements = $latestRevisions->sum('general_requirements');
        $management_reserve = $latestRevisions->sum('management_reserve');
        $profit = $eac_contracts_value - $budget_cost;

        $revision1 = [
            'budget_cost' => $budget_cost,
            'direct_cost' => $budget_cost - $general_requirements - $management_reserve,
            'indirect_cost' => $general_requirements + $management_reserve,
            'eac_contracts_value' => $eac_contracts_value,
            'profit' => $profit,
            'profitability' => $profit * 100 / $eac_contracts_value,
        ];

        return compact('revision0', 'revision1');
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

    private function cost_info()
    {
        $cpis = MasterShadow::join('periods as p', 'master_shadows.period_id', '=', 'p.id')
            ->join('projects', 'master_shadows.project_id', '=', 'projects.id')
            ->select(['master_shadows.project_id', 'projects.name', 'projects.project_code'])
            ->selectRaw('sum(budget_cost) as budget_cost, sum(allowable_ev_cost) as allowable_cost, sum(to_date_cost) as to_date_cost')
            ->selectRaw('sum(completion_cost) as completion_cost, SUM(CASE WHEN resource_type_id = 8 THEN budget_cost END) as reserve')
            ->whereIn('period_id', $this->last_period_ids)
            ->groupBy(['master_shadows.project_id','projects.name', 'projects.project_code'])
            ->get()->map(function ($period) {
//                $progress = min(1, $period->to_date_cost / ($period->budget_cost - $period->reserve));
//                $reserve = $progress * $period->reserve;
                $period->allowable_cost = $this->projects_summary->get($period->project_id)->sum('allowable_cost');
                $period->cpi = $period->allowable_cost / $period->to_date_cost;

                $period->variance = $period->allowable_cost - $period->to_date_cost;
                $revision = BudgetRevision::where('project_id', $period->project_id)->latest()->first();

                if ($revision) {
                    $period->eac_contract_amount = $revision->eac_contract_amount;
                } else {
                    $period->eac_contract_amount = $period->project->eac_contract_amount;
                }

                return $period;
            })->sortBy('cpi');

        $allowable_cost = $this->cost_summary->sum('allowable_cost');
        $to_date_cost = $this->cost_summary->sum('to_date_cost');
        $variance = $allowable_cost - $to_date_cost;
        $cpi = 0;
        if ($to_date_cost) {
            $cpi = $allowable_cost / $to_date_cost;
        }
        $pw_index = $this->waste_index_trend()->get($this->period->name, 0);

        $highest_risk = $cpis->first();
        $lowest_risk = $cpis->last();

        $total_budget = $this->cost_summary->sum('budget_cost');
        $to_date = $this->cost_summary->sum('to_date_cost');

//        $actual_progress = round($to_date * 100 / $total_budget, 2);
        $contract_value = $this->projects->sum('revised_contract_amount');
        $actual_progress = $this->earned_value * 100 / $contract_value;  //round($this->period->actual_progress, 2);
        $planned_progress = $this->planned_value * 100 / $contract_value; //round($this->period->planned_progress ?: 0, 2);

        $progress = [$actual_progress, $planned_progress];

        $total_eac = $cpis->sum('eac_contract_amount');
        $eac_profit = $total_eac - $this->cost_summary->sum('completion_cost');
        $eac_profitability = 0;
        if ($total_eac) {
            $eac_profitability = $eac_profit * 100 / $total_eac;
        }

        $spi_index = 0;
        if ($this->planned_value) {
            $spi_index = $this->earned_value / $this->planned_value;
        }

        return compact(
            'allowable_cost', 'to_date_cost', 'variance', 'cpi', 'spi_index',
            'highest_risk', 'lowest_risk', 'pw_index', 'progress', 'eac_profit', 'eac_profitability'
        );
    }

    private function cost_summary()
    {
        $general_activities = StdActivity::where('division_id', 779)->pluck('id')->implode(',');

        $fields = [
            "project_id, (CASE WHEN activity_id in ($general_activities) THEN 'INDIRECT' WHEN activity_id = 3060 THEN 'MANAGEMENT RESERVE' ELSE 'DIRECT' END) AS 'type'",
            'sum(budget_cost) budget_cost', 'sum(to_date_cost) as to_date_cost', 'sum(allowable_ev_cost) as allowable_cost',
            'sum(allowable_var) as to_date_var', 'sum(remaining_cost) as remaining_cost', 'sum(completion_cost) as completion_cost',
            'sum(cost_var) as completion_cost_var', 'sum(prev_cost) as previous_cost',
            'sum(completion_cost_optimistic) as completion_cost_optimistic', 'sum(completion_cost_likely) as completion_cost_likely',
            'sum(completion_cost_pessimistic) as completion_cost_pessimistic',
        ];

        $this->projects_summary = MasterShadow::whereIn('period_id', $this->last_period_ids)
            ->selectRaw(implode(', ', $fields))
            ->groupBy('project_id')->groupBy('type')->get()
            ->groupBy('project_id')->map(function ($group) {
                return $group->keyBy('type');
            })->map(function ($project) {
                $reserve = $project->get('MANAGEMENT RESERVE');
                if ($reserve->budget_cost) {
                    $reserve->completion_cost = $reserve->remaining_cost = 0;
                    $reserve->completion_cost_var = $reserve->budget_cost;

                    $progress = min(1, $project->sum('to_date_cost') / ($project->sum('budget_cost') - $reserve->budget_cost));
                    $reserve->to_date_var = $reserve->allowable_cost = $progress * $reserve->budget_cost;
                }
                return $project;
            });

        $this->cost_summary = $this->projects_summary->reduce(function($summary, $project) {
                $types = ['DIRECT', "INDIRECT", 'MANAGEMENT RESERVE'];
                $fields = [
                    'budget_cost', 'to_date_cost', 'allowable_cost', 'to_date_var',
                    'remaining_cost', 'completion_cost', 'completion_cost_var',
                    'completion_cost_optimistic', 'completion_cost_likely', 'completion_cost_pessimistic',
                ];

                foreach ($types as $type) {
                    if (!$summary->has($type)) {
                        $summary->put($type, new Fluent(compact('type')));
                    }

                    foreach ($fields as $field) {
                        $typeData = $summary->get($type);
                        $typeData->$field += $project->get($type)->$field;
                    }
                }

                return $summary;
            }, collect());

        if ($this->cost_summary->has('MANAGEMENT RESERVE')) {
            $reserve = $this->cost_summary->get('MANAGEMENT RESERVE');
            $reserve->completion_cost_optimistic = $reserve->remaining_cost = 0;
            $reserve->completion_cost_likely = 0;
            $reserve->completion_cost_pessimistic = 0;
            $reserve->completion_cost_var = $reserve->budget_cost;
            $reserve->completion_cost_var_optimistic = $reserve->budget_cost;
            $reserve->completion_cost_var_likely = $reserve->budget_cost;
            $reserve->completion_cost_var_pessimistic = $reserve->budget_cost;
        }

        $this->completionValues = [
            $this->cost_summary->sum('completion_cost_optimistic'),
            $this->cost_summary->sum('completion_cost_likely'),
            $this->cost_summary->sum('completion_cost_pessimistic'),
        ];

        sort($this->completionValues);

        return $this->cost_summary;
    }

    private function cost_percentage()
    {
        $to_date_cost = $this->cost_summary->sum('to_date_cost');
        $remaining_cost = $this->cost_summary->sum('remaining_cost');
        $sum = $to_date_cost + $remaining_cost;

        return collect([round($to_date_cost * 100 / $sum, 2), round($remaining_cost * 100 / $sum, 2)]);
    }

    private function cpi_trend()
    {
        return $this->cpi_trend = MasterShadow::from('master_shadows as sh')
            ->selectRaw('p.global_period_id, sum(budget_cost) as budget_cost, sum(to_date_cost) as to_date_cost')
            ->selectRaw('sum(allowable_ev_cost) as allowable_cost, sum(CASE WHEN activity_id = 3060 THEN budget_cost END) as total_reserve')
            ->join('periods as p', 'sh.period_id', '=', 'p.id')
            ->whereIn('sh.period_id', $this->trend_period_ids)
            ->groupBy('sh.period_id')
            ->orderBy('p.global_period_id')
            ->get()->map(function ($period) {
                $progress = min(1, $period->to_date_cost / ($period->budget_cost - $period->total_reserve));
                $reserve = $period->total_reserve * $progress;
                $period->allowable_cost += $reserve;
                return $period;
            })->groupBy('global_period_id')->map(function($group, $period_id) {
                $period = new Fluent;
                $period->cpi_index = round($group->sum('allowable_cost') / $group->sum('to_date_cost'), 3);
                $period->name = $this->trend_global_periods->get($period_id)->name;
                return $period;
            });
    }

    function spi_trend()
    {
//        return $this->trend_global_periods->sortBy('end_date')->pluck('spi_index', 'name')->filter();
        return $this->trend_global_periods->keyBy('name')->map(function($period) {
            $periods = $period->periods()->readyForReporting()->selectRaw('max(id) as id, project_id, planned_progress, actual_progress')->groupBy(['project_id', 'planned_progress', 'actual_progress'])->get();

            $planned_value = $periods->sum('planned_value');
            $earned_value = $periods->sum('earned_value');

            if (!$planned_value) {
                return 0;
            }

            return $earned_value / $planned_value;
        })->filter()->reverse();
    }

    function waste_index_trend()
    {
        if ($this->waste_index_trend !== null) {
            return $this->waste_index_trend;
        }

        return $this->waste_index_trend = MasterShadow::from('master_shadows as sh')
            ->select('p.global_period_id')
            ->selectRaw('sum((sh.allowable_qty - sh.to_date_qty) * sh.to_date_unit_price) as variance, sum(sh.allowable_ev_cost) as allowable_cost')
            ->join('periods as p', 'sh.period_id', '=', 'p.id')
            ->whereIn('sh.period_id', $this->trend_period_ids)
            ->where('resource_type_id', 3)
            ->groupBy('p.global_period_id')
            ->orderBy('p.global_period_id')
            ->get()->map(function ($period) {
                $period->waste_index = 0;
                if ($period->allowable_cost) {
                    $period->waste_index = round(abs($period->variance * 100 / $period->allowable_cost), 4);
                }

                $period->name = $this->trend_global_periods->get($period->global_period_id)->name;
                return $period;
            })->pluck('waste_index', 'name');
    }

    function productivity_index_trend()
    {
        return GlobalPeriod::latest('end_date')->where('id', '>=', 12)->take(6)->get()->sortBy('end_date')->pluck('productivity_index', 'name')->filter();

//        $global_period_ids = $periods->pluck('id');
//        $period_ids = Period::whereIn('global_period_id', $global_period_ids)->readyForReporting()->pluck('id');
//
//        return MasterShadow::from('master_shadows as sh')
//            ->join('periods as p', 'sh.period_id', '=', 'p.id')
//            ->join('cost_man_days as md', function (JoinClause $on) {
//                $on->where('sh.period_id', '=', 'md.period-id')
//                    ->where('sh.wbs_id', '=', 'md.wbs_id')
//                    ->where('sh.activity_id', '=', 'md.activity_id');
//            })
//            ->selectRaw("p.global_period_id, sum(budget_unit) as budget_unit, sum(allowable_qty) as allowable_qty, sum(actual) as actual")
//            ->where('sh.resource_type_id', 2)->where('to_date_cost', '>', 0)
//            ->whereIn('sh.period_id', $period_ids)
//            ->orderBy('p.global_period_id')
//            ->groupBy('p.global_period_id')
//            ->get()->map(function ($period) use ($periods) {
//                $period->name = $periods->get($period->global_period_id)->name;
//                $period->pi = 0;
//                if ($period->actual) {
//                    $period->pi = $period->allowable_qty / $period->actual;
//                }
//
//                return $period;
//            })->pluck('pi', 'name');
    }

    function revenue_statement()
    {
        return [
            $this->planned_value = $this->periods->sum('planned_value'),
            $this->earned_value = $this->periods->sum('earned_value'),
            $this->actual_invoice_value = $this->periods->sum('actual_invoice_value'),
        ];
//        $periods = GlobalPeriod::latest()->take(12)->get()->keyBy('id');
//        $global_period_ids = $periods->pluck('id');
//        $period_ids = Period::whereIn('global_period_id', $global_period_ids)->readyForReporting()->pluck('id');
//
//        return ActualRevenue::from('actual_revenue as r')
//            ->join('periods as p', 'r.period_id', '=', 'p.id')
//            ->whereIn('period_id', $period_ids)
//            ->selectRaw('p.global_period_id, sum(value) as value')
//            ->groupBy('p.global_period_id')->get(['value', 'global_period_id'])
//            ->map(function ($period) use ($periods) {
//                $period->name = $periods->get($period->global_period_id)->name;
//                return $period;
//            })->pluck('value', 'name');
    }

    function pdf()
    {
        $output_file = $this->createPdf();

        if (!$output_file) {
            flash('Failed to generate PDF');
            return redirect()->to(request()->path());
        }

        return response()->download($output_file, 'kps-dashboard.pdf')
            ->deleteFileAfterSend(true);
    }

    function createPdf()
    {
        if (!env('CHROME_PATH')) {
            return false;
        }

        $data = $this->run();
        $data['print'] = 1;

        $content = view('dashboard.index', $data)->render();
        $str_random = str_random(8);
        $filename = storage_path('app/public/' . $str_random . '.html');
        file_put_contents($filename, $content);

        $chrome = escapeshellcmd(env('CHROME_PATH'));
        $filepath = url('/storage/' . basename($filename));
        $output_file = storage_path('app/' . $str_random . '.pdf');

        exec($command = "'$chrome' --headless --window-size=1280,720 --disable-gpu --print-to-pdf='$output_file' '$filepath' 2>/dev/null");

        @unlink($filename);

        return file_exists($output_file)? $output_file : false;
    }
}