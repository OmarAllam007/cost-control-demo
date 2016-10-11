<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 2:54 PM
 */

namespace App\Http\Controllers\Reports;


use App\Project;

class ActivityResourceBreakDown
{
    public function getActivityResourceBreakDown(Project $project)
    {
        $break_downs = $project->breakdowns()->get();
        $data = [];
        foreach ($break_downs as $break_down) {
            $wbs = $break_down->wbs_level;
            if (!isset($data[ $wbs->id ])) {
                $data[ $wbs->id ] = [
                    'name' => $wbs->name,
                    'parents' => [],//
                    'activities' => [
                        'names' => [],
                        'cost_account' => [
                            'cost_account' => '',
                            'resources' => [],
                            'price' => 0,
                            'budget_unit' => 0,
                            'budget_cost' => 0,
                        ],
                    ],
                ];
            }
        }
        foreach ($data as $key => $value) {
            $parent = $wbs;
            while ($parent->parent_id && $parent->id != $parent->parent_id) {
                $parent = $parent->parent;
                $data[ $key ]['parents'] [] = $parent->id;
            }
        }//get parents of wbs-level


        dd($data);

        return view('std-activity.activity_resource_breakdown', compact('project'));

    }

}