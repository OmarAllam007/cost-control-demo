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
use App\Unit;
use App\WbsLevel;

class QuantitiySurveySummery
{
    public function qsSummeryReport(Project $project)
    {
        $break_downs = $project->breakdowns()->get();

        $level_array = [];
        foreach ($break_downs as $break_down) {
            $boq_item = Boq::where('cost_account', $break_down->cost_account)->first();
            $division_name = ActivityDivision::where('id', $break_down->std_activity->division->id)->get();
            if (!isset($level_array[ $break_down->wbs_level->id ])) {
                $level_array[ $break_down->wbs_level->id ] = [
                    'id' => $break_down->wbs_level->id,
                    'name' => $break_down->wbs_level->name,
                    'activity_divisions' => [
                        'division' => $division_name->pluck('name'),
                        'activity_names' => [$break_down->std_activity->name],
                        'boq_item_description' => $boq_item->description,
                        'cost_account' => $boq_item->cost_account,
                        'budget_qty' => 0,
                        'eng_qty' => 0,
                        'unit'=>'',
                    ],

                ];

            }
            foreach ($break_down->resources as $resource) {
                $level_array[ $break_down->wbs_level->id ]['activity_divisions']['budget_qty'] = $resource->budget_qty;
                $level_array[ $break_down->wbs_level->id ]['activity_divisions']['eng_qty']  = $resource->eng_qty;
                $level_array[ $break_down->wbs_level->id ]['activity_divisions']['unit'] = Unit::find($resource->qty_survey->unit_id)->type;
            }
        }

        return view('reports.quantity_survey', compact('project', 'level_array'));
    }
}