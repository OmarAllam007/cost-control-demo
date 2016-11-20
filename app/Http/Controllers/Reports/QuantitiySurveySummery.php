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
use App\Project;
use App\StdActivity;
use App\Survey;
use App\Unit;
use App\WbsLevel;

class QuantitiySurveySummery
{
    public function qsSummeryReport(Project $project)
    {
        $break_downs_resources = $project->breakdown_resources()->with('breakdown.std_activity','breakdown.std_activity.division','breakdown.wbs_level','template_resource.resource')->get();

        $level_array = [];
        foreach ($break_downs_resources as $break_down_resource) {
            $wbs_level = $break_down_resource->breakdown->wbs_level;
            $std_activity = $break_down_resource->breakdown->std_activity;
            $boq_item = Boq::where('cost_account', $break_down_resource->breakdown->cost_account)->first();
            $qs = Survey::where('cost_account', $break_down_resource->breakdown->cost_account)->first();
            $division_name = $std_activity->division->name;
            $activity_name = $std_activity->name;

            if (!isset($level_array[ $wbs_level->id ])) {
                $level_array[ $wbs_level->id ] = [
                    'id' => $wbs_level->id,
                    'name' => $wbs_level->name,
                    'activity_divisions' => [
                    ],
                ];
            }
            if (!isset($level_array[ $wbs_level->id ]['activity_divisions'][ $division_name ])) {
                $level_array[ $wbs_level->id ]['activity_divisions'][ $division_name ]['name'] = $division_name;

            }
            if (!isset($level_array[ $wbs_level->id ]['activity_divisions'][ $division_name ]['activities'][ $std_activity->id ])) {
                $level_array[ $wbs_level->id ]['activity_divisions'][ $division_name ]['activities'][ $std_activity->id ] = [
                    'name' => $activity_name,
                    'cost_accounts' => [],
                ];

            }

            if (!isset($level_array[ $wbs_level->id ]['activity_divisions'][ $division_name ]['activities'][ $std_activity->id ]['cost_accounts'][ $break_down_resource->cost_account ])) {
                $level_array[ $wbs_level->id ]['activity_divisions'][ $division_name ]['activities'][ $std_activity->id ]['cost_accounts'][ $break_down_resource->cost_account ] =

                    [
                        'cost_account' => $break_down_resource->cost_account,
                        'boq_name' => $boq_item->description,
                        'budget_qty' => $break_down_resource->budget_qty,
                        'eng_qty' => $break_down_resource->eng_qty,
                        'unit' => isset(Unit::find($qs->id)->type) ? Unit::find($qs->id)->type : '',
                    ];
            }


        }

        return view('reports.quantity_survey', compact('project', 'level_array'));
    }
}