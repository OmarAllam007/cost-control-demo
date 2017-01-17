<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/9/2016
 * Time: 9:16 PM
 */

namespace App\Http\Controllers\Reports;


use App\ActivityDivision;
use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use App\Survey;
use App\Unit;
use App\WbsLevel;

class QuantitiySurveySummery
{
    public $root_ids = [];
    public $project;

    public function qsSummeryReport(Project $project)
    {
        set_time_limit(300);
        $this->project = $project;
        $levels = BreakDownResourceShadow::with('wbs')->where('project_id', $project->id)->pluck('wbs_id')->toArray();
        $wbs_levels = WbsLevel::whereIn('id', $levels)->where('project_id', $project->id)->get();
        $tree = [];
        foreach ($wbs_levels as $level) {
            if ($level->root) {
                $treeLevel = $this->buildTree($level->root);
            }
            if ($treeLevel) {
                $tree[] = $treeLevel;
            }
        }
        return view('reports.budget.qs_summery.qs_summery_report', compact('project', 'tree'));

    }

    private function buildTree($level)
    {
        $tree = [];
        if (!in_array($level->id, $this->root_ids)) {
            $this->root_ids[] = $level->id;
            $tree = ['id' => $level->id,'code'=>$level->code, 'name' => $level->name, 'children' => [], 'divisions' => []];


            $break_downs_resources = BreakDownResourceShadow::with('std_activity')->with('std_activity.division')->where('project_id', $this->project->id)
                ->where('wbs_id', $level->id)->get();

            foreach ($break_downs_resources as $break_down_resource) {
                $std_activity = $break_down_resource->std_activity;
                $boq_item = Boq::where('cost_account', $break_down_resource['cost_account'])->first();
//                $qs = Survey::where('cost_account', $break_down_resource['cost_account'])->first();
                $division_name = $std_activity->division->name;
                $division_id = $std_activity->division->id;
                $activity_name = $break_down_resource['activity'];
                $activity_id = $break_down_resource['activity_id'];

                if (!isset($tree['divisions'][$division_id])) {
                    $tree['divisions'][$division_id] = [
                        'name' => $division_name,
                        'activities' => [],
                    ];
                }

                if (!isset($tree['divisions'][$division_id]['activities'][$activity_id])) {
                    $tree['divisions'][$division_id]['activities'][$activity_id] = [
                        'name' => $activity_name,
                        'cost_accounts' => [],
                    ];
                }
                if (!isset($tree['divisions'][$division_id]['activities'][$activity_id]['cost_accounts'][$break_down_resource['cost_account']])) {
                    $tree['divisions'][$division_id]['activities'][$activity_id]['cost_accounts'][$break_down_resource['cost_account']]= [
                        'cost_account' => $break_down_resource['cost_account'],
                        'boq_name' => $boq_item->description,
                        'budget_qty' => $break_down_resource['budget_qty'],
                        'eng_qty' => $break_down_resource['eng_qty'],
                        'unit' => $boq_item->unit->type??'',
                    ];
                }
            }


            if ($level->children->count()) {
                $tree['children'] = $level->children->map(function (WbsLevel $childLevel) {
                    return $this->buildTree($childLevel);
                });
            }
        }
        return $tree;
    }
}