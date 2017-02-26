<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 24/12/16
 * Time: 07:39 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\ActivityDivision;
use App\Boq;
use App\Breakdown;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\StdActivity;
use App\WbsLevel;

class BoqReport
{
    protected $root_ids = [];
    protected $divisions;
    protected $project;
    protected $activities;
    protected $div_activities;
    protected $prev_activities;
    protected $wbs_levels;
    protected $budget_data;
    protected $cost_data;
    protected $activities_ids;
    protected $cost_accounts;
    protected $boqs;

    function getReport(Project $project, $period_id)
    {
        $this->project = $project;
        $this->divisions = collect();
        $this->activities = collect();
        $this->div_activities = collect();
        $this->prev_activities = collect();
        $this->boqs = collect();
        $this->wbs_levels = collect();
        $this->budget_data = collect();
        $this->cost_data = collect();
        $this->activities_ids = [];
        $this->cost_accounts = [];
        $tree = [];

        $wbs_levels = \Cache::get('wbs-tree-' . $project->id) ?: $project->wbs_tree;
        Boq::where('project_id', $project->id)->get()->map(function ($boq) {
            $this->boqs->put($boq->wbs_id . $boq->cost_account, ['price_unit' => $boq->price_ur, 'description' => $boq->description, 'quantity' => $boq->quantity]);
        });
        collect(\DB::select('SELECT cost_account, wbs_id,activity_id,
  SUM(boq_equivilant_rate) AS boq_equivilant_rate,
  SUM(budget_cost) budget_cost,
  SUM(budget_unit) budget_unit
FROM break_down_resource_shadows
WHERE project_id=?
GROUP BY cost_account,wbs_id,activity_id', [$project->id]))->map(function ($shadow) {
            $this->budget_data->put(trim(str_replace(' ', '', $shadow->wbs_id)) . trim(str_replace(' ', '', $shadow->activity_id)) . trim(str_replace(' ', '', $shadow->cost_account)),
                ['boq_equivilant_rate' => $shadow->boq_equivilant_rate, 'budget_cost' => $shadow->budget_cost,
                    'budget_unit' => $shadow->budget_unit]);
        });

        collect(\DB::select('SELECT
  sh.activity_id,
  sh.cost_account,
  sh.wbs_id,
  SUM(to_date_unit_price) AS to_date_unit_price,
  SUM(physical_unit)      AS physical_unit,
  SUM(to_date_cost)       AS to_date_cost,
  SUM(allowable_ev_cost)  AS allowable_ev_cost,
  SUM(allowable_var)  AS allowable_var,
  SUM(remaining_cost)  AS remaining_cost,
  SUM(completion_cost)  AS completion_cost,
  SUM(cost_var)  AS completion_cost_var
FROM cost_shadows c, break_down_resource_shadows sh
WHERE c.project_id = ? AND c.period_id = ?
      AND sh.breakdown_resource_id = c.breakdown_resource_id
GROUP BY sh.activity_id,
  sh.cost_account,
  sh.wbs_id', [$project->id, $period_id]))->map(function ($cost) {
            $this->cost_data->put(trim(str_replace(' ', '', $cost->wbs_id)) . trim(str_replace(' ', '', $cost->activity_id)) . trim(str_replace(' ', '', $cost->cost_account)), [
                'to_date_unit_price' => $cost->to_date_unit_price
                , 'physical_unit' => $cost->physical_unit
                , 'to_date_cost' => $cost->to_date_cost
                , 'allowable_ev_cost' => $cost->allowable_ev_cost
                , 'allowable_var' => $cost->allowable_var
                , 'remaining_cost' => $cost->remaining_cost
                , 'completion_cost' => $cost->completion_cost
                , 'cost_var' => $cost->completion_cost_var

            ]);
        });
        collect(\DB::select('SELECT DISTINCT activity_id , wbs_id FROM break_down_resource_shadows sh
WHERE project_id=?', [$project->id]))->map(function ($item) {

            if (!isset($this->activities_ids[$item->wbs_id]['activities'])) {
                $this->activities_ids[$item->wbs_id] = ['activities' => []];
            }
            if (!isset($this->activities_ids[$item->wbs_id]['activities'][$item->activity_id])) {
                $this->activities_ids[$item->wbs_id]['activities'][$item->activity_id] = ['id' => $item->activity_id];
            }
            return $this->activities_ids;
        });

        collect(\DB::select('SELECT sh.cost_account , activity_id , wbs_id 
FROM  break_down_resource_shadows sh 
WHERE sh.project_id =?', [$project->id]))->map(function ($item) {
            if (!isset($this->cost_accounts[$item->wbs_id . $item->activity_id]['cost_accounts'])) {
                $this->cost_accounts[$item->wbs_id . $item->activity_id] = ['cost_accounts' => []];
            }
            if (!isset($this->cost_accounts[$item->wbs_id . $item->activity_id]['cost_accounts'][$item->cost_account])) {
                $this->cost_accounts[$item->wbs_id . $item->activity_id]['cost_accounts'][$item->cost_account] = $item->cost_account;
            }
            return $this->cost_accounts;
        });

        $this->divisions = ActivityDivision::all()->keyBy('id')->map(function ($division) {
            return $division;
        });

        collect(\DB::select('SELECT activity,
  sh.activity_id,
  sh.wbs_id,
  SUM(sh.budget_cost) AS budget_cost,
  SUM(cost.to_date_cost) AS to_date_cost,
  SUM(cost.allowable_ev_cost) AS allowable_cost,
  SUM(allowable_var) AS allowable_var,
  SUM(remaining_cost) AS remain_cost,
  SUM(completion_cost) AS completion_cost
FROM break_down_resource_shadows sh JOIN cost_shadows cost
WHERE sh.breakdown_resource_id = cost.breakdown_resource_id AND sh.project_id = ? AND cost.period_id=?
GROUP BY activity_id , sh.wbs_id', [$project->id, $period_id]))->map(function ($activity) {
            $this->activities->put($activity->activity_id . $activity->wbs_id, ['name' => $activity->activity, 'budget_cost' => $activity->budget_cost,
                'to_date_cost' => $activity->to_date_cost, 'allowable_cost' => $activity->allowable_cost, 'allowable_var' => $activity->allowable_var
                , 'remain_cost' => $activity->remain_cost, 'completion_cost' => $activity->completion_cost
            ]);
        });

        $this->wbs_levels = WbsLevel::where('project_id', $project->id)->get()->keyBy('id')->map(function ($level) {
            return $level;
        });
        collect(\DB::select('SELECT activity,
  sh.activity_id,
  sh.wbs_id,
  SUM(cost.to_date_cost) AS to_date_cost,
  SUM(cost.allowable_ev_cost) AS allowable_cost,
  SUM(allowable_var) AS allowable_var
FROM break_down_resource_shadows sh JOIN cost_shadows cost
WHERE sh.breakdown_resource_id = cost.breakdown_resource_id AND sh.project_id = ? AND cost.period_id < ?
GROUP BY activity_id , sh.wbs_id', [$project->id, $period_id]))->map(function ($activity) {
            $this->prev_activities->put($activity->activity_id . $activity->wbs_id, ['name' => $activity->activity,
                'to_date_cost' => $activity->to_date_cost, 'allowable_cost' => $activity->allowable_cost, 'allowable_var' => $activity->allowable_var
            ]);
        });

        $this->div_activities = StdActivity::all()->keyBy('id')->map(function ($activity) {
            $parent = $activity->division;
            while ($parent->parent) {
                $parent = $parent->parent;
            }
            return $parent;
        });

        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildTree($level);
            $tree[] = $treeLevel;
        }

        return view('reports.cost-control.boq-report.boq_report', compact('tree', 'levels', 'project'));
    }

    function buildTree($level)
    {

        $tree = ['id' => $level['id'], 'name' => $level['name'], 'children' => [], 'division' => [], 'data' => []];
        $activities_id = collect($this->activities_ids)->get($level['id'])['activities'];
        if ($activities_id) {
            $activities_id = array_keys($activities_id);
            $activities = StdActivity::whereIn('id', $activities_id)->get();

            foreach ($activities as $activity) {
                $division = $this->div_activities->get($activity->id);

                if (!isset($tree['division'][$division->id])) {
                    $tree['division'][$division->id] = ['name' => $division->name, 'cost_accounts' => []];
                }

                $cost_accounts = collect($this->cost_accounts)->get($level['id'] . $activity->id);

                foreach ($cost_accounts['cost_accounts'] as $key => $cost_account) {
                    $quantity = $this->boqs->get($level['id'] . $key)['quantity'];
                    $price_ur = $this->boqs->get($level['id'] . $key)['price_unit'];
                    $description = $this->boqs->get($level['id'] . $key)['description'];
                    $boq_equavalent_rate = $this->budget_data->get($level['id'] . $activity->id . $key)['boq_equivilant_rate'];
                    $budget_cost = $this->budget_data->get($level['id'] . $activity->id . $key)['budget_cost'];
                    $budget_unit = $this->budget_data->get($level['id'] . $activity->id . $key)['budget_unit'];
                    $to_date_price_unit = $this->cost_data->get($level['id'] . $activity->id . $key)['to_date_unit_price'];
                    $physical_unit = $this->cost_data->get($level['id'] . $activity->id . $key)['physical_unit'];
                    $to_date_cost = $this->cost_data->get($level['id'] . $activity->id . $key)['to_date_cost'];
                    $allowable_ev_cost = $this->cost_data->get($level['id'] . $activity->id . $key)['allowable_ev_cost'];
                    $allowable_var = $this->cost_data->get($level['id'] . $activity->id . $key)['allowable_var'];
                    $remaining_cost = $this->cost_data->get($level['id'] . $activity->id . $key)['remaining_cost'];
                    $completion_cost = $this->cost_data->get($level['id'] . $activity->id . $key)['completion_cost'];
                    $completion_cost_var = $this->cost_data->get($level['id'] . $activity->id . $key)['cost_var'];

                    if ($quantity == null) {
                        $level = $this->wbs_levels->get($level['id']);
                        $parent = $level;
                        while ($parent->parent) {
                            $parent = $parent->parent;
                            $quantity = $this->boqs->get($parent->id . $key)['quantity'];
                            $price_ur = $this->boqs->get($parent->id . $key)['price_unit'];
                            $description = $this->boqs->get($parent->id . $key)['description'];

                            if ($quantity != null || $price_ur != null) {
                                break;
                            }
                        }
                    }

                    if (!isset($tree['division'][$division->id]['cost_accounts'][$key])) {
                        $tree['division'][$division->id]['cost_accounts'][$key] = [
                            'cost_account' => $key,
                            'description' => $description,
                            'unit_price' => $price_ur,
                            'quantity' => $quantity,
                            'equavlant' => $boq_equavalent_rate,
                            'budget_unit' => $budget_unit,
                            'budget_cost' => $budget_cost,
                            'to_date_unit_price' => $to_date_price_unit,
                            'physical_unit' => $physical_unit,
                            'to_date_cost' => $to_date_cost,
                            'allowable_cost' => $allowable_ev_cost,
                            'to_date_cost_var' => $allowable_var,
                            'remaining_cost' => $remaining_cost,
                            'at_comp' => $completion_cost,
                            'at_comp_var' => $completion_cost_var
                        ];
                    }
                }

            }
        }

        if (count($level['children'])) {
            $tree['children'] = collect($level['children'])->map(function ($childLevel) {

                return $this->buildTree($childLevel);
            });
        }


        return $tree;
    }


}