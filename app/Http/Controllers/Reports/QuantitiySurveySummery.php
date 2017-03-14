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
use Barryvdh\Reflection\DocBlock\Type\Collection;

class QuantitiySurveySummery
{
    public $root_ids = [];
    public $project;

    public $boqs;
    public $survies;

    public function qsSummeryReport(Project $project)
    {

        set_time_limit(300);
        $this->project = $project;

        $this->boqs = Boq::where('project_id', $this->project->id)->get()->keyBy('cost_account')->map(function ($boq) {
            return $boq->description;
        });

        $this->survies = Survey::where('project_id', $this->project->id)->get()->keyBy('cost_account')->map(function ($survey) {
            return $survey->unit->type;
        });

        $wbs_levels = WbsLevel::where('project_id', $project->id)->tree()->get();
        $tree = [];
        foreach ($wbs_levels as $level) {
            $treeLevel = $this->buildTree($level->root);
            $tree[] = $treeLevel;
        }
        return view('reports.budget.qs_summery.qs_summery_report', compact('project', 'tree'));
    }


    private function buildTree($level)
    {


        $tree = ['id' => $level->id, 'code' => $level->code, 'name' => $level->name, 'children' => [], 'divisions' => []];

        $break_downs_resources = BreakDownResourceShadow::with('std_activity', 'std_activity.division')->where('project_id', $this->project->id)
            ->where('wbs_id', $level->id)->get();

        foreach ($break_downs_resources as $break_down_resource) {
            $std_activity = $break_down_resource->std_activity;
            $boq_item = $this->boqs->get($break_down_resource['cost_account']);
            $survey_item = $this->survies->get($break_down_resource['cost_account']);
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
                $tree['divisions'][$division_id]['activities'][$activity_id]['cost_accounts'][$break_down_resource['cost_account']] = [
                    'cost_account' => $break_down_resource['cost_account'],
                    'boq_name' => $boq_item,
                    'budget_qty' => $break_down_resource['budget_qty'],
                    'eng_qty' => $break_down_resource['eng_qty'],
                    'unit' => $survey_item,
                ];
            }
        }


        if ($level->children->count()) {
            $tree['children'] = $level->children->map(function (WbsLevel $childLevel) {
                return $this->buildTree($childLevel);
            });
        }

        return $tree;
    }
}