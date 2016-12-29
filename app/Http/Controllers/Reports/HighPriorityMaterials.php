<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/12/2016
 * Time: 8:06 AM
 */

namespace App\Http\Controllers\Reports;


use App\Project;
use App\Unit;
use Illuminate\Http\Request;

class HighPriorityMaterials
{
    public function getTopHighPriorityMaterials(Project $project, Request $request)
    {

        $break_down_resources = $project->breakdown_resources()->with('template_resource', 'breakdown', 'breakdown.qty_survey')->get();
        $data = [];
        $visible = true;
        $generate = false;
        $button = false;
        foreach ($break_down_resources as $break_down_resource) {
            $resource = $break_down_resource->resource;
            if(isset($resource->types)){
                $root = $resource->types->root;
                $resource_type = $resource->types;
                if ($root->name == '03.MATERIAL') {

                    if (!isset($data[$resource_type->name])) {
                        $data[$resource_type->name] = [
                            'name' => $resource_type->name,
                            'budget_cost' => 0,
                            'budget_unit' => 0,
                            'unit' => '',
                        ];
                    }
                    if (!isset($data[$resource_type->name]['resources'][$resource->id])) {
                        $data[$resource_type->name]['resources'][$resource->id] = [
                            'resource_id' => $resource->id,
                            'name' => $resource->name,
                            'budget_cost' => 0,
                        ];
                    }
                    $data[$resource_type->name]['resources'][$resource->id]['budget_cost'] += $break_down_resource->budget_cost;
                    $data[$resource_type->name]['budget_cost'] += $break_down_resource->budget_cost;
                    $data[$resource_type->name]['unit'] = Unit::find($resource->unit)->type;
                    $data[$resource_type->name]['budget_unit'] += $break_down_resource->budget_unit;
                }
            }


        }

        if (!is_null($data)) {
            usort($data, function ($a, $b) {
                return $b['budget_cost'] - $a['budget_cost'];
            });
        }




        /** divisions checked*/

        \Session::forget('checked');
        if ($request->checked) {
            \Session::set('keys', $request->checked);
            foreach ($data as $key => $value) {
                if (in_array($key, $request->checked)) {
                    continue;
                } else {
                    unset($data[$key]);
                }
            }
            $visible = false;

        }


        /** checked resources */
        if ($request->resources) {
            $session = \Session::get('keys');
//            $resources_session = session('resources');

            foreach ($data as $key => $value) {

                if (in_array($key, $session)) {
                    continue;
                } else {
                    unset($data[$key]);
                }
            }
            foreach ($data as $key => $value) {
                foreach ($value['resources'] as $rKey => $resource) {
                    if (in_array($rKey, $request->resources)) {
                       continue;
                    }
                    else{
                        $data[$key]['budget_cost'] -= $data[$key]['resources'][$rKey]['budget_cost'];
                        unset($data[$key]['resources'][$key]);
                    }


                }
                if ($data[$key]['budget_cost'] <= 0) {
                    unset($data[$key]);
                }
            }

            $generate = false;
            $button = true;

        }

        return view('reports.high_priority_materials', compact('data', 'project', 'visible', 'generate', 'button'));
    }


}