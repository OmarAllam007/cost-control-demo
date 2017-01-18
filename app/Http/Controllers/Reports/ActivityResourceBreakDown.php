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
        set_time_limit(300);
        $breakDown_resources = BreakDownResourceShadow::where('project_id', $project->id)->with('breakdown','resource', 'wbs')->get();
        $project_total = 0;
        $data = [];
        foreach ($breakDown_resources as $breakDown_resource) {

            $break_down = $breakDown_resource->breakdown;
            $wbs_level = $breakDown_resource->wbs->name;
            $std_activity_item = $breakDown_resource['activity'];
            $std_activity_id = $breakDown_resource['activity_id'];


            $boq = Boq::where('cost_account', $breakDown_resource['cost_account'])->first();
            if ($boq) {
                $boq = $boq->description;
            }
            if (!isset($data[$wbs_level])) {
                $data[$wbs_level] = [
                    'activities' => [],
                    'activities_total_cost' => 0,
                ];
            }
            if (!isset($data[$wbs_level]['activities'][$std_activity_item])) {
                $data[$wbs_level]['activities'][$std_activity_item] = [
                    'id'=>$std_activity_id,
                    'name' => $std_activity_item,
                    'activity_total_cost' => 0,
                    'cost_accounts' => [],
                ];
            }

            if (!isset($data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account])) {
                $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account] = [
                    'cost_account' => $break_down->cost_account,
                    'account_total_cost' => 0,
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
            }
            $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account]['resources'][$breakDown_resource['resource_name']]['budget_cost']
                += $breakDown_resource['budget_cost'];

            $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts'][$break_down->cost_account]
            ['resources'][$breakDown_resource['resource_name']]['budget_unit']
                += $breakDown_resource['budget_unit'];

            $data[$wbs_level]['activities'][$std_activity_item]['cost_accounts']
            [$break_down->cost_account]['resources'][$breakDown_resource['resource_name']]['price_unit']
                = $breakDown_resource['unit_price'];

//            $project_total += $breakDown_resource['budget_cost'];
            ksort($data[$wbs_level]['activities'][$std_activity_item]['cost_accounts']);

        }

        foreach ($data as $key => $value) {
            foreach ($value['activities'] as $activityKey => $activity) {
                foreach ($activity['cost_accounts'] as $accountKey => $account) {
                    foreach ($account['resources'] as $resourceKey => $resource) {
                        $data[$key]['activities'][$activityKey]['cost_accounts'][$accountKey]['account_total_cost'] += $resource['budget_cost'];
                    }
                    $data[$key]['activities'][$activityKey]['activity_total_cost'] += $data[$key]['activities'][$activityKey]['cost_accounts'][$accountKey]['account_total_cost'];
                }
                $data[$key]['activities_total_cost'] += $data[$key]['activities'][$activityKey]['activity_total_cost'];
            }
        }
        ksort($data);
        return view('std-activity.activity_resource_breakdown', compact('data', 'project', 'project_total'));

    }
}