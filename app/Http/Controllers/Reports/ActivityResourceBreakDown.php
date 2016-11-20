<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 2:54 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Project;
use App\Survey;
use App\Unit;

class ActivityResourceBreakDown
{
    public function getActivityResourceBreakDown(Project $project)
    {
        $breakDown_resources = $project->breakdown_resources()->with('breakdown.std_activity','breakdown.wbs_level','template_resource.resource')->get();
        $data = [];
        foreach ($breakDown_resources as $breakDown_resource) {
            $break_down = $breakDown_resource->breakdown;
            $wbs_level = $break_down->wbs_level->name;
            $std_activity_item = $break_down->std_activity->name;
            $resource = $breakDown_resource->resource;
            $boq = Boq::where('cost_account',$break_down->cost_account)->first()->description;

            if (!isset($data[ $wbs_level ])) {
                $data[ $wbs_level ] = [
                    'activities' => [],
                ];
            }
            if (!isset($data[ $wbs_level ]['activities'][ $std_activity_item ])) {
                $data[ $wbs_level ]['activities'][ $std_activity_item ] = [
                    'name' => $std_activity_item,
                    'cost_accounts' => [],
                ];
            }

            if (!isset($data[ $wbs_level ]['activities'][ $std_activity_item ]['cost_accounts'][ $break_down->cost_account ])) {
                $data[ $wbs_level ]['activities'][ $std_activity_item ]['cost_accounts'][ $break_down->cost_account ] = [
                    'cost_account' => $break_down->cost_account,
                    'boq_description'=>$boq,
                    'resources' => [],
                ];
            }
            if (!isset($data[ $wbs_level ]['activities'][ $std_activity_item ]['cost_accounts'][ $break_down->cost_account ]['resources'][ $resource->name ])) {
                $data[ $wbs_level ]['activities'][ $std_activity_item ]['cost_accounts'][ $break_down->cost_account ]['resources'][ $resource->name ] = [
                    'name' => $resource->name,
                    'unit'=>isset($breakDown_resource->qty_survey->unit_id)?Unit::find($breakDown_resource->qty_survey->unit_id)->type:'',
                    'price_unit' => 0,
                    'budget_cost' => 0,
                    'budget_unit' => 0,
                ];
                $data[ $wbs_level ]['activities'][ $std_activity_item ]['cost_accounts'][ $break_down->cost_account ]['resources'][ $resource->name ]['budget_cost'] +=$breakDown_resource->budget_cost;

                $data[ $wbs_level ]['activities'][ $std_activity_item ]['cost_accounts'][ $break_down->cost_account ]['resources'][ $resource->name ]['budget_unit'] +=$breakDown_resource->budget_unit;

                    $data[ $wbs_level ]['activities'][ $std_activity_item ]['cost_accounts'][ $break_down->cost_account ]['resources'][ $resource->name ]['price_unit'] = $breakDown_resource->project_resource->rate;
            }


        }
        return view('std-activity.activity_resource_breakdown', compact('data', 'project'));

    }
}