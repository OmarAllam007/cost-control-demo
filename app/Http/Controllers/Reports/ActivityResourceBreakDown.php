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

class ActivityResourceBreakDown
{
    public function getActivityResourceBreakDown(Project $project)
    {
        $breakDown_resources = BreakDownResourceShadow::where('project_id', $project->id)->with('resource','wbs')->get();
        $data = [];
        foreach ($breakDown_resources as $breakDown_resource) {

            $break_down = $breakDown_resource->breakdown;
            $wbs_level = $breakDown_resource->wbs->name;
            $std_activity_item = $breakDown_resource['activity'];
            $resource = $breakDown_resource->resource;
            $boq = Boq::where('cost_account', $breakDown_resource['cost_account'])->first()->description;

            if (!isset($data[$wbs_level])) {
                $data[$wbs_level] = [
                    'activities' => [],
                ];
            }
            if (!isset($data[$wbs_level]['activities'][$std_activity_item])) {
                $data[$wbs_level]['activities'][$std_activity_item] = [
                    'name' => $std_activity_item,
                    'cost_accounts' => [],
                ];
            }

            if (!isset($data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account])) {
                $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account] = [
                    'cost_account' => $break_down->cost_account,
                    'boq_description' => $boq,
                    'resources' => [],
                ];
            }
            ksort($data[$wbs_level]['activities']);
            if (!isset($data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account]['resources'][$breakDown_resource['resource_name']])) {
                $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account]['resources'][$breakDown_resource['resource_name']] = [
                    'name' => $breakDown_resource['resource_name'],
                    'unit' => $breakDown_resource['measure_unit'],
                    'price_unit' => 0,
                    'budget_cost' => 0,
                    'budget_unit' => 0,
                ];
                $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account]['resources'][$breakDown_resource['resource_name']]['budget_cost']
                    += $breakDown_resource['budget_cost'];

                $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account]
                ['resources'][$breakDown_resource['resource_name']]['budget_unit']
                    += $breakDown_resource['budget_unit'];

                $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts']
                [$break_down->cost_account]['resources'][$breakDown_resource['resource_name']]['price_unit']
                    = $breakDown_resource['boq_equivilant_rate'];

            }
            ksort($data[$wbs_level]['activities'][$std_activity_item]['cost_accounts']);

        }
        ksort($data);
        return view('std-activity.activity_resource_breakdown', compact('data', 'project'));

    }
}