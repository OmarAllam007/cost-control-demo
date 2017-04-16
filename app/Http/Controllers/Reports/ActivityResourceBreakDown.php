<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 2:54 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;
use App\Unit;
use App\WbsLevel;

class ActivityResourceBreakDown
{
    public $boqs;
    public $project;

    public function getActivityResourceBreakDown(Project $project)
    {
        set_time_limit(300);
        $this->project = $project;
        $project_total = BreakDownResourceShadow::where('project_id', $project->id)->get()->sum('budget_cost');
        $wbs_levels = $project->wbs_tree;

        $this->boqs = Boq::where('project_id', $project->id)->get()->keyBy('cost_account')->map(function ($boq) {
            return $boq->description;
        });

        $tree = [];
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->getReportTree($level);
            $tree[] = $treeLevel;
        }
        return view('reports.budget.activity_resource_breakdown.activity_resource_breakdown', compact('tree','project', 'project_total'));

    }


    private function getReportTree(WbsLevel $level)
    {
        $tree = ['id' => $level->id, 'code' => $level->code, 'name' => $level->name, 'children' => [], 'activities' => [], 'activities_total_cost' => 0];

        $breakDown_resources = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->where('wbs_id', $level->id)->get();

        foreach ($breakDown_resources as $breakDown_resource) {
            $std_activity_item = $breakDown_resource['activity'];
            $std_activity_id = $breakDown_resource['activity_id'];

            if ($this->boqs->has($breakDown_resource['cost_account'])) {
                $boq = $this->boqs->get($breakDown_resource['cost_account']);
            }

            if (!isset($tree['activities'][$std_activity_item])) {
                $tree['activities'][$std_activity_item] = [
                    'id'=>$std_activity_id,
                    'name' => $std_activity_item,
                    'activity_total_cost' => 0,
                    'cost_accounts' => [],
                ];
            }

            if (!isset($tree['activities'][$std_activity_item]['cost_accounts'][$breakDown_resource['cost_account']])) {
                $tree['activities'][$std_activity_item]['cost_accounts'][$breakDown_resource['cost_account']] = [
                    'cost_account' => $breakDown_resource['cost_account'],
                    'account_total_cost' => 0,
                    'boq_description' => $boq ?? 0,
                    'resources' => [],
                ];
            }
            ksort($tree['activities']);
            if (!isset($tree['activities'][$std_activity_item]['cost_accounts'][$breakDown_resource['cost_account']]['resources'][$breakDown_resource['resource_name']])) {
                $tree['activities'][$std_activity_item]['cost_accounts'][$breakDown_resource['cost_account']]['resources'][$breakDown_resource['resource_name']] = [
                    'name' => $breakDown_resource['resource_name'],
                    'unit' => $breakDown_resource['measure_unit'],
                    'resource_type'=>$breakDown_resource['resource_type'],
                    'price_unit' => 0,
                    'budget_cost' => 0,
                    'budget_unit' => 0,

                ];
            }

            $tree['activities'][$std_activity_item]['activity_total_cost']
                += $breakDown_resource['budget_cost'];


            $tree['activities'][$std_activity_item]['cost_accounts'][$breakDown_resource['cost_account']]['resources'][$breakDown_resource['resource_name']]['budget_cost']
                += $breakDown_resource['budget_cost'];

            $tree['activities'][$std_activity_item]['cost_accounts'][$breakDown_resource['cost_account']]
            ['resources'][$breakDown_resource['resource_name']]['budget_unit']
                += $breakDown_resource['budget_unit'];

            $tree['activities'][$std_activity_item]['cost_accounts']
            [$breakDown_resource['cost_account']]['resources'][$breakDown_resource['resource_name']]['price_unit']
                = $breakDown_resource['unit_price'];

        }

        $tree['activities_total_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->whereIn('wbs_id', $level->getChildrenIds())->get()->sum('budget_cost');

        if ($level->children && $level->children->count()) {
            $tree['children'] = $level->children->map(function (WbsLevel $childLevel) {
                return $this->getReportTree($childLevel);
            });
        }
        return $tree;
    }
}