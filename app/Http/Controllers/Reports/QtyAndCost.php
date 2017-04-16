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
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\Facades\DB;

class QtyAndCost
{
    private $data;
    private $dry;
    private $project;
    private $activities;
    private $cost_account_budget_cost;
    private $codes;


    public function compare(Project $project)
    {
        set_time_limit(600);
        $this->project = $project;
        $this->data = [];
        $this->codes = collect();
        $this->cost_account_budget_cost = collect();

        $this->activities = StdActivity::all()->keyBy('id')->map(function ($activity) {
            return $activity->discipline;
        });


        $this->dry = Boq::where('project_id', $project->id)->get()->keyBy('wbs_id')->map(function ($boq) {
            return $boq->dry_ur;
        });

        collect(\DB::select('SELECT DISTINCT  sum(budget_cost) budget_cost , cost_account  FROM break_down_resource_shadows WHERE project_id=?
GROUP BY cost_account', [$project->id]))->each(function ($shadow) {
            $this->cost_account_budget_cost->put($shadow->cost_account, $shadow->budget_cost);
        });


        $total = [
            'left_eq' => 0,
            'right_eq' => 0,
        ];

        BreakDownResourceShadow::with(['boq', 'std_activity', 'survey'])->where('project_id', $project->id)->chunk(10000, function ($shadows) {
            foreach ($shadows as $shadow) {
                if (!isset($this->data[$shadow->std_activity->discipline])) {
                    $this->data[$shadow->std_activity->discipline] = ['left_eq' => 0, 'right_eq' => 0];
                }
                if ($shadow->boq_id != 0 && $shadow->boq_wbs_id != 0) {

                    $code = $shadow->boq_id . $shadow->boq_wbs_id;
                    if (!$this->codes->has($code)) {
                        $budget_cost = BreakDownResourceShadow::where('project_id',$this->project->id)
                            ->where('wbs_id',$shadow->wbs_id)->where('cost_account',$shadow->cost_account)->sum('budget_cost');
                        $this->data[$shadow->std_activity->discipline]['left_eq'] += ($budget_cost - $shadow->boq->dry_ur) * $shadow->boq->quantity;
                        $this->data[$shadow->std_activity->discipline]['right_eq'] += ($shadow->survey->budget_qty - $shadow->boq->quantity) * $budget_cost;
                    }
                    $this->codes->put($code, $code);
                }
            }
        });

        foreach ($this->data as $item) {
            $total['left_eq'] += $item['left_eq'];
            $total['right_eq'] += $item['right_eq'];
        }
        $data = $this->data;
        ksort($data);
        return view('reports.qty_and_cost', compact('data', 'total', 'project'));
    }

    private function buildReport($level)
    {
        $inital_data = [];

        $tree = ['id' => $level->id, 'code' => $level->code, 'name' => $level->name, 'children' => [], 'budget_cost' => 0, 'budget_rate' => 0];

        if ($level->getDry()) {
            $budget = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('wbs_id', $level->id)->first();
            if ($budget) {
                $shadows = BreakDownResourceShadow::where('project_id', $this->project->id)
                    ->where('wbs_id', $level->id)->get();

                foreach ($shadows as $shadow) {
                    if (!isset($inital_data[$this->activities->get($shadow->activity_id)][$shadow->cost_account])) {
                        $inital_data[$this->activities->get($shadow->activity_id)][$shadow->cost_account] = [
                            'budget' => 0,
                            'dry' => $this->cost_accounts->get($shadow->cost_account)['dry'],
                            'qty' => $this->cost_accounts->get($shadow->cost_account)['qty'],
                            'budget_qty' => $shadow->budget_qty,
                        ];
                    }
                    $inital_data[$this->activities->get($shadow->activity_id)][$shadow->cost_account]['budget'] += $shadow->budget_cost;
                }
                foreach ($inital_data as $key => $items) {
                    foreach ($items as $item) {
                        if ($item['budget_qty'] != 0) {
                            $rate = ($item['budget'] / $item['budget_qty']);
                        } else {
                            $rate = ($item['budget']);
                        }
                        if (!isset($this->data[$key]['left']) || !isset($this->data[$key]['right'])) {
                            $this->data[$key]['left'] = 0;
                            $this->data[$key]['right'] = 0;
                        }
                        $this->data[$key]['left'] += ($rate - $item['dry']) * $item['qty'];
                        $this->data[$key]['right'] += ($item['budget_qty'] - $item['qty']) * $rate;
                    }
                }
            } else {
                foreach ($level->children as $child) {
                    $shadows = BreakDownResourceShadow::where('project_id', $this->project->id)
                        ->whereIn('wbs_id', $child->id)->get();
                    foreach ($shadows as $shadow) {
                        if (!isset($inital_data[$this->activities->get($shadow->activity_id)][$shadow->cost_account])) {
                            $inital_data[$this->activities->get($shadow->activity_id)][$shadow->cost_account] = [
                                'budget' => 0,
                                'dry' => $this->cost_accounts->get($shadow->cost_account)['dry'],
                                'qty' => $this->cost_accounts->get($shadow->cost_account)['qty'],
                                'budget_qty' => $shadow->budget_qty,
                            ];
                        }
                        $inital_data[$this->activities->get($shadow->activity_id)][$shadow->cost_account]['budget'] += $shadow->budget_cost;
                    }
                    foreach ($inital_data as $key => $items) {
                        foreach ($items as $item) {
                            if ($item['budget_qty'] != 0) {
                                $rate = ($item['budget'] / $item['budget_qty']);
                            } else {
                                $rate = ($item['budget']);
                            }
                            if (!isset($this->data[$key]['left']) || !isset($this->data[$key]['right'])) {
                                $this->data[$key]['left'] = 0;
                                $this->data[$key]['right'] = 0;
                            }
                            $this->data[$key]['left'] += ($rate - $item['dry']) * $item['qty'];
                            $this->data[$key]['right'] += (($item['budget_qty'] - $item['qty'])) * $rate;
                        }
                    }
                }

            }
        }

        if ($level->children && $level->children->count()) {
            $tree['children'] = $level->children->map(function (WbsLevel $childLevel) use ($tree) {
                return $this->buildReport($childLevel);
            });
        }


        return $tree;
    }


}