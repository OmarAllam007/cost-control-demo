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
use App\Unit;
use App\WbsLevel;

class QuantitiySurveySummery
{
    public function qsSummeryReport(Project $project)
    {
        $break_downs_resources = $project->breakdown_resources()->get();


        $level_array = [];
        foreach ($break_downs_resources as $break_down_resource) {
            $boq_item = Boq::where('cost_account', $break_down_resource->breakdown->cost_account)->first();
            $division_name = ActivityDivision::find(StdActivity::find($break_down_resource->stdactivityid)->division_id)->name;
            $activity_name = StdActivity::find($break_down_resource->stdactivityid)->name;
            if (!isset($level_array[ $break_down_resource->wbslevelid ])) {
                $level_array[ $break_down_resource->wbslevelid ] = [
                    'id' => $break_down_resource->wbslevelid,
                    'name' => $break_down_resource->breakdown->wbs_level->name,
                    'activity_divisions' => [
                    ],
                ];
            }
            if (!isset($level_array[ $break_down_resource->wbslevelid ]['activity_divisions'][ $division_name ])) {
                $level_array[ $break_down_resource->wbslevelid ]['activity_divisions'][ $division_name ]['name'] = $division_name;

            }
            if (!isset($level_array[ $break_down_resource->wbslevelid ]['activity_divisions'][ $division_name ]['activities'][ $break_down_resource->stdactivityid ])) {
                $level_array[ $break_down_resource->wbslevelid ]['activity_divisions'][ $division_name ]['activities'][ $break_down_resource->stdactivityid ] = [
                    'name' => $activity_name,
                    'cost_accounts' => [],
                ];

            }

            if (!isset($level_array[ $break_down_resource->wbslevelid ]['activity_divisions'][ $division_name ]['activities'][ $break_down_resource->stdactivityid ]['cost_accounts'][ $break_down_resource->cost_account ])) {
                $level_array[ $break_down_resource->wbslevelid ]['activity_divisions'][ $division_name ]['activities'][ $break_down_resource->stdactivityid ]['cost_accounts'][ $break_down_resource->cost_account ] = [
                    'cost_account' => $break_down_resource->cost_account,
                    'boq_name' =>$boq_item->description,
                    'budget_qty'=>$break_down_resource->budget_qty,
                    'eng_qty'=>$break_down_resource->eng_qty,
                    'unit'=>isset(Unit::find($boq_item->id)->type)?Unit::find($boq_item->id)->type:'',
                ];
            }


        }
        return view('reports.quantity_survey', compact('project', 'level_array'));
    }
}