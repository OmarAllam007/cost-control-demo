<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 27/12/16
 * Time: 11:19 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\ActualRevenue;
use App\Boq;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Project;
use App\StdActivity;
use App\WbsLevel;
use Illuminate\Support\Facades\DB;

class OverdraftReport
{
    protected $project;
    protected $boqs;
    protected $period_id;
    protected $actual_data;
    protected $cost_account_data;
    protected $wbs_levels;

    function getDraft (Project $project, $period_id)
    {
        $this->period_id = $period_id;
        $this->boqs = collect();
        $this->actual_data = collect();
        $this->cost_account_data = collect();

        Boq::where('project_id', $project->id)->get()->map(function ($boq) {
            $this->boqs->put($boq->wbs_id . $boq->cost_account, ['quantity' => $boq->quantity, 'price_ur' => $boq->price_ur, 'description' => $boq->description]);
        });
        $this->wbs_levels = WbsLevel::where('project_id', $project->id)->get()->keyBy('id')->map(function ($level) {
            return $level;
        });
        ActualRevenue::where('project_id', $project->id)->where('period_id', '<=', $period_id)->get()->map(function ($actual) {
            $this->actual_data->put($actual->wbs_id . $actual->cost_account, ['quantity' => $actual->quantity, 'actual_revenue' => $actual->value]);
        });
        //main_data
        collect(\DB::select('SELECT * FROM (
       SELECT
         budget.cost_account,
         budget.wbs_id,
         SUM(physical_unit)                                                                            AS ph_unit,
         SUM((cost.to_date_cost - cost.cost_variance_to_date_due_unit_price) / budget.boq_equivilant_rate) AS ph_unit_e
       FROM cost_shadows AS cost

         LEFT JOIN break_down_resource_shadows AS budget ON (cost.breakdown_resource_id = budget.breakdown_resource_id)
       WHERE cost.project_id = ? AND cost.period_id = (SELECT max(p.period_id)
                                                        FROM cost_shadows p
                                                        WHERE p.breakdown_resource_id = cost.breakdown_resource_id AND
                                                              cost.period_id <= ?)
       GROUP BY 1,2) AS data
GROUP BY 1,2;', [$project->id, $period_id]))->map(function ($shadow) {
            $this->cost_account_data->put(trim($shadow->wbs_id . $shadow->cost_account), ['ph_unit' => $shadow->ph_unit, 'ph_unit_e' => $shadow->ph_unit_e]);
        });

        $this->project = $project;
        $wbs_levels = \Cache::get('wbs-tree-' . $project->id) ?: WbsLevel::where('project_id', $project->id)->tree()->get();
        $tree = [];
        foreach ($wbs_levels as $wbs_level) {
            $levelTree = $this->buildTree($wbs_level);
            $tree[] = $levelTree;
        }

        return view('reports.cost-control.over-draft.over_draft', compact('tree', 'project'));
    }

    protected function buildTree ($wbs_level)
    {

        $tree = ['id' => $wbs_level['id'], 'name' => $wbs_level['name'], 'children' => [], 'divisions' => [], 'data' => []];

        $activitiy_ids = collect(\DB::select('SELECT sh.activity_id
FROM cost_shadows cost
  JOIN break_down_resource_shadows sh ON cost.breakdown_resource_id = sh.breakdown_resource_id
WHERE sh.project_id = ?
      AND wbs_level_id = ?', [$this->project->id, $wbs_level['id']]))->map(function ($activity) {
            return $activity->activity_id;
        });

        $activities = StdActivity::whereIn('id', $activitiy_ids)->get();
        foreach ($activities as $activity) {
            $division = $activity->division;
            if (!isset($tree['divisions'][$division->id])) {
                $tree['divisions'][$division->id] = ['name' => $division->name, 'activities' => []];
            }
            if (!isset($tree['divisions'][$division->id]['activities'][$activity->id])) {
                $tree['divisions'][$division->id]['activities'][$activity->id] = ['activity_name' => $activity->name, 'cost_accounts' => [], 'estimated_qty' => 0,
                    'actual_qty' => 0,
                    'price_ur' => 0,
                    'physical_unit' => 0,
                    'physical_unit_e' => 0,
                    'physical_revenue' => 0,
                    'physical_revenue_e' => 0,
                    'actual_revenue_cost' => 0,
                    'variance' => 0,
                    'variance_e' => 0
                ];
            }
            $cost_accounts = collect(\DB::select('SELECT sh.cost_account
FROM cost_shadows cost JOIN break_down_resource_shadows sh ON cost.breakdown_resource_id = sh.breakdown_resource_id
WHERE sh.project_id =?
      AND activity_id = ? AND sh.wbs_id = ?', [$this->project->id, $activity->id, $wbs_level['id']]));

            foreach ($cost_accounts as $cost_account) {

                $quantity = $this->boqs->get($wbs_level['id'] . $cost_account->cost_account)['quantity'];
                $price_ur = $this->boqs->get($wbs_level['id'] . $cost_account->cost_account)['price_ur'];
                $description = $this->boqs->get($wbs_level['id'] . $cost_account->cost_account)['description'];

                if ($quantity == null) {
                    $level = $this->wbs_levels->get($wbs_level['id']);
                    $parent = $level;
                    while ($parent->parent) {
                        $parent = $parent->parent;

                        $quantity = $this->boqs->get($parent->id . $cost_account->cost_account)['quantity'];
                        $price_ur = $this->boqs->get($parent->id . $cost_account->cost_account)['price_ur'];
                        $description = $this->boqs->get($parent->id . $cost_account->cost_account)['description'];
                        if ($quantity && $price_ur) {
                            break;
                        }
                    }
                }

                $physical_revenue = $price_ur * $this->cost_account_data->get(trim($wbs_level['id'] . $cost_account->cost_account))['ph_unit'];
                $physical_revenue_e = $price_ur * $this->cost_account_data->get(trim($wbs_level['id'] . $cost_account->cost_account))['ph_unit_e'];

                $actual_qty = $this->actual_data->get($wbs_level['id'] . $cost_account->cost_account)['quantity'];
                $actual_revenue = $this->actual_data->get($wbs_level['id'] . $cost_account->cost_account)['actual_revenue'];

                $variance = ($actual_revenue - $physical_revenue);
                $variance_e = ($actual_revenue - $physical_revenue_e);

                if (!isset($tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account])) {
                    $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account] = [
                        'cost_account' => $cost_account->cost_account,
                        'description' => $description,
                        'estimated_qty' => $quantity,
                        'actual_qty' => $actual_qty,
                        'price_ur' => $price_ur,
                        'physical_unit' => $this->cost_account_data->get(trim($wbs_level['id'] . $cost_account->cost_account))['ph_unit'],
                        'physical_unit_e' => $this->cost_account_data->get(trim($wbs_level['id'] . $cost_account->cost_account))['ph_unit_e'],
                        'physical_revenue' => $physical_revenue,
                        'physical_revenue_e' => $physical_revenue_e,
                        'actual_revenue_cost' => $actual_revenue,
                        'variance' => $variance,
                        'variance_e' => $variance_e,
                    ];
                }

                $tree['divisions'][$division->id]['activities'][$activity->id]['estimated_qty'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['estimated_qty'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['actual_qty'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['actual_qty'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['price_ur'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['price_ur'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['price_ur'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['price_ur'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['physical_unit'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['physical_unit'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['physical_unit_e'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['physical_unit_e'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['physical_revenue'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['physical_revenue'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['physical_revenue_e'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['physical_revenue_e'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['variance'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['variance'];
                $tree['divisions'][$division->id]['activities'][$activity->id]['variance_e'] += $tree['divisions'][$division->id]['activities'][$activity->id]['cost_accounts'][$cost_account->cost_account]['variance_e'];
            }

        }

        if (count($wbs_level['children'])) {
            $tree['children'] = collect($wbs_level['children'])->map(function ($childLevel) {
                return $this->buildTree($childLevel);
            });
        }
        return $tree;
    }
}