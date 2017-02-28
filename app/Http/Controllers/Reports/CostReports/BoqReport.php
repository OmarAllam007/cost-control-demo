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
    protected $wbs_level_children;
    protected $boqs_project;

    function getReport(Project $project, $period_id)
    {
        //get Boq By wbs_id , cost account ... done
        //

        $this->project = $project;
        $this->divisions = collect();
        $this->activities = collect();
        $this->div_activities = collect();
        $this->prev_activities = collect();
        $this->boqs_project = collect();
        $this->boqs = collect();
        $this->wbs_levels = collect();
        $this->wbs_level_children = collect();
        $this->budget_data = collect();
        $this->cost_data = collect();
        $this->activities_ids = [];
        $this->cost_accounts = [];
        $tree = [];

        $wbs_levels = \Cache::get('wbs-tree-' . $project->id) ?: $project->wbs_tree;

        Boq::where('project_id', $project->id)->get()->map(function ($boq) {
            $this->boqs->put($boq->wbs_id . $boq->cost_account, ['price_unit' => $boq->price_ur, 'description' => $boq->description, 'quantity' => $boq->quantity, 'dry_ur' => $boq->dry_ur]);
        });
        Boq::where('project_id', $project->id)->get()->map(function ($boq) {
            $this->boqs_project->put($boq->wbs_id, $boq->price_ur);
        });

        $this->wbs_level_children = WbsLevel::where('project_id', $project->id)->get()->keyBy('id')->map(function ($level) {
            return $level->getChildrenIds();
        });

        //get_budget_data
        collect(\DB::select('SELECT cost_account, wbs_id,activity_id,
  budget_qty AS budget_qty,
  SUM(boq_equivilant_rate) AS boq_equivilant_rate,
  SUM(budget_cost) budget_cost,
  SUM(budget_unit) budget_unit,
  SUM(budget_cost) / budget_qty AS budget_unit_rate
FROM break_down_resource_shadows
WHERE project_id=?
GROUP BY cost_account,wbs_id,activity_id , budget_qty', [$project->id]))->map(function ($shadow) {
            $this->budget_data->put(trim(str_replace(' ', '', $shadow->wbs_id)) . trim(str_replace(' ', '', $shadow->activity_id)) . trim(str_replace(' ', '', $shadow->cost_account)),
                ['boq_equivilant_rate' => $shadow->boq_equivilant_rate, 'budget_cost' => $shadow->budget_cost,
                    'budget_unit' => $shadow->budget_unit, 'budget_unit_rate' => $shadow->budget_unit_rate ,'budget_qty'=>$shadow->budget_qty]);
        });
        //end


        //get_cost_data
        collect(\DB::select('SELECT
  sh.activity_id,
  sh.cost_account,
  sh.wbs_id,
  SUM(physical_unit)      AS physical_unit,
  SUM(to_date_cost)       AS to_date_cost,
  SUM(allowable_ev_cost)  AS allowable_cost,
  SUM(allowable_var)  AS to_date_cost_var,
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
                'physical_unit' => $cost->physical_unit
                , 'to_date_cost' => $cost->to_date_cost
                , 'allowable_ev_cost' => $cost->allowable_cost
                , 'to_date_cost_var' => $cost->to_date_cost_var
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
        $level = $this->wbs_levels->get($level['id']);
        $boq = Boq::where('project_id', $this->project->id)->where('wbs_id', $level['id'])->first();
        if ($boq) {
            $children = $this->wbs_level_children->get($level['id']);
            foreach ($children as $child) {
                $activities_id = collect($this->activities_ids)->get($child)['activities'];
                if ($activities_id) {
                    $activities_id = array_keys($activities_id);
                    foreach ($activities_id as $activity) {
                        $division = $this->div_activities->get($activity);
                        if (!isset($tree['division'][$division->id])) {
                            $tree['division'][$division->id] = ['name' => $division->name, 'cost_accounts' => []];
                        }

                        $cost_accounts = collect($this->cost_accounts)->get($child . $activity);
                        foreach ($cost_accounts['cost_accounts'] as $key => $cost_account) {
                            $dry = $this->boqs->get($level['id'] . $key)['dry_ur'];
                            $quantity = $this->boqs->get($level['id'] . $key)['quantity'];
                            $price_ur = $this->boqs->get($level['id'] . $key)['price_unit'];
                            $description = $this->boqs->get($level['id'] . $key)['description'];
                            $budget_unit_rate = $this->budget_data->get($child . $activity . $key)['budget_unit_rate'];
                            $budget_cost = $this->budget_data->get($child . $activity . $key)['budget_cost'];
                            $budget_unit = $this->budget_data->get($child . $activity . $key)['budget_unit'];
                            $budget_qty = $this->budget_data->get($child . $activity . $key)['budget_qty'];
                            $physical_unit = $this->cost_data->get($child . $activity . $key)['physical_unit'];
                            $to_date_cost = $this->cost_data->get($child . $activity . $key)['to_date_cost'];
                            $allowable_ev_cost = $this->cost_data->get($child . $activity . $key)['allowable_ev_cost'];
                            $to_date_cost_var = $this->cost_data->get($child . $activity . $key)['to_date_cost_var'];
                            $remaining_cost = $this->cost_data->get($child . $activity . $key)['remaining_cost'];
                            $completion_cost = $this->cost_data->get($child . $activity . $key)['completion_cost'];
                            $completion_cost_var = $this->cost_data->get($child . $activity . $key)['cost_var'];
                            $todate_budget_unit_rate = $physical_unit!=0 ? $to_date_cost/$physical_unit:$budget_unit_rate;
                            if (!isset($tree['division'][$division->id]['cost_accounts'][$key])) {
                                $tree['division'][$division->id]['cost_accounts'][$key] = [
                                    'cost_account'=>$key,
                                    'dry' =>0,
                                    'budget_unit_rate' =>0,
                                    'todate_budget_unit_rate' =>0,
                                    'var_unit_rate' =>0,
                                    'description' =>$description,
                                    'unit_price' =>0,
                                    'quantity' =>0,
                                    'budget_unit' =>0,
                                    'budget_cost' =>0,
                                    'physical_unit' =>0,
                                    'to_date_cost' =>0,
                                    'allowable_cost' =>0,
                                    'to_date_cost_var' =>0,
                                    'remaining_cost' =>0,
                                    'at_comp' =>0,
                                    'at_comp_var' =>0,
                                    'dry_cost'=>0,
                                    'boq_cost'=>0,
                                    'budget_qty'=>0,


                                ];
                            }
                        }

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