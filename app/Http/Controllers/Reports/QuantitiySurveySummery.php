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
    public function qsSummeryReport(Project $project)
    {
        $break_downs_resources = BreakDownResourceShadow::where('project_id', $project->id)->with('wbs', 'std_activity')->get();
        $level_array = [];
        foreach ($break_downs_resources as $break_down_resource) {
            $wbs_level = $break_down_resource->wbs;
            $std_activity = $break_down_resource->std_activity;
            $boq_item = Boq::where('cost_account', $break_down_resource['cost_account'])->first();
            $qs = Survey::where('cost_account', $break_down_resource['cost_account'])->first();
            $division_name = $std_activity->division->name;
            $activity_name = $break_down_resource['activity'];
            $activity_id = $break_down_resource['activity_id'];

            if (!isset($level_array[$wbs_level->id])) {
                $level_array[$wbs_level->id] = [
                    'id' => $wbs_level->id,
                    'name' => $wbs_level->name,
                    'activity_divisions' => [
                    ],
                ];
            }
            if (!isset($level_array[$wbs_level->id]['activity_divisions'][$division_name])) {
                $level_array[$wbs_level->id]['activity_divisions'][$division_name]['name'] = $division_name;

            }
            if (!isset($level_array[$wbs_level->id]['activity_divisions'][$division_name]['activities'][$std_activity->id])) {
                $level_array[$wbs_level->id]['activity_divisions'][$division_name]['activities'][$std_activity->id] = [
                    'activity_id' => $activity_id,
                    'name' => $activity_name,
                    'cost_accounts' => [],
                ];
            }


            if (!isset($level_array[$wbs_level->id]['activity_divisions'][$division_name]['activities'][$std_activity->id]['cost_accounts'][$break_down_resource['cost_account']])) {
                $level_array[$wbs_level->id]['activity_divisions'][$division_name]['activities'][$std_activity->id]['cost_accounts'][$break_down_resource['cost_account']] =

                    [
                        'cost_account' => $break_down_resource['cost_account'],
                        'boq_name' => $boq_item->description,
                        'budget_qty' => $break_down_resource['budget_qty'],
                        'eng_qty' => $break_down_resource['eng_qty'],
                        'unit' => $break_down_resource['measure_unit'],
                    ];
            }


        }

        $divisions = [];
        $activities = [];
        foreach ($level_array as $key => $value) {
            foreach ($value['activity_divisions'] as $divKey => $divValue) {
                foreach ($divValue['activities'] as $actKey => $actValue) {
                    if (!in_array($divKey, $divisions)) {
                        $divisions [] = $divKey;
                    } else {
                        unset($level_array[$key]['activity_divisions'][$divKey]['name']);
                    }

                    if (!in_array($actValue['name'], $activities)) {
                        $activities [] = $actValue['name'];
                    } else {
                        unset($level_array[$key]['activity_divisions'][$divKey]['activities'][$actKey]['name']);
                    }


                }

            }


        }

        return view('reports.quantity_survey', compact('project', 'level_array'));

    }
}