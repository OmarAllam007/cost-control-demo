<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 4:25 PM
 */

namespace App\Http\Controllers\Reports;


use App\BreakDownResourceShadow;
use App\BusinessPartner;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\Unit;
use Make\Makers\Resource;

class ResourceDictionary
{
    public function getResourceDictionary(Project $project)
    {
        set_time_limit(300);
        $break_down_resources = BreakDownResourceShadow::where('project_id', $project->id)->with('resource.parteners', 'resource', 'wbs', 'resource.types')->get();
        $data = [];
        $parents = [];


        foreach ($break_down_resources as $break_down_resource) {
            $resource = $break_down_resource->resource;
            if (isset($resource->types)) {
                $division = $resource->types;


                $root = $break_down_resource['resource_type'];
                if (!isset($data[$root])) {
                    $data[$root] = [
                        'name' => $root,
                        'type_id' => $break_down_resource['resource_type_id'],
                        'divisions' => [],
                    ];
                }

                if (!isset($data[$root]['divisions'][$division->id])) {
                    $data[$root]['divisions'][$division->id] = [
                        'name' => $division->name,
                        'parents' => [],
                        'resources' => [],
                    ];
                }
                $parent = $division;
                while ($parent->parent) {
                    $parent = $parent->parent;
                    if ($parent->name == $root) {
                        continue;
                    }
                    if (!isset($data[$root]['divisions'][$division->id]['parents'][$parent->id])) {
                        $data[$root]['divisions'][$division->id]['parents'][$parent->id] = [
                            'name' => $parent->name
                        ];
                    }

                }

                $latest_resource = Resources::orderBy('created_at', 'desc')->where('resource_id', $resource->id)->get()->first();
                if (!isset($data[$root]['divisions'][$division->id]['resources'][$resource->id])) {
                    $data[$root]['divisions'][$division->id]['resources'][$resource->id] = [
                        'code' => $break_down_resource['resource_code'],
                        'name' => $break_down_resource['resource_name'],
                        'rate' => !is_null($latest_resource) ? $latest_resource->rate : $resource->rate,
                        'unit' => $break_down_resource['measure_unit'],
                        'waste' => $break_down_resource['resource_waste'],
                        'reference' => $resource->reference,
                        'partner' => isset($resource->parteners->name) ? $resource->parteners->name : '',
                        'budget_cost' => 0,
                        'budget_unit' => 0,
                    ];
                }
                $data[$root]['divisions'][$division->id]['resources'][$break_down_resource['resource_id']]['budget_cost'] += $break_down_resource->budget_cost;
                $data[$root]['divisions'][$division->id]['resources'][$resource->id]['budget_unit'] += $break_down_resource->budget_unit;
            }
        }


        foreach ($data as $key => $value) {
            foreach ($value['divisions'] as $dKey => $dValue) {
                ksort($data[$key]['divisions'][$dKey]['resources']);
                foreach ($dValue['parents'] as $pKey => $parent) {
                    if (!in_array($parent['name'], $parents)) {
                        $parents[] = $parent['name'];
                    } else {
                        unset($data[$key]['divisions'][$dKey]['parents'][$pKey]);
                    }

                }
                ksort($data[$key]['divisions'][$dKey]['parents']);
            }
        }
        ksort($data);
        return view('reports.resource_dictionary', compact('project', 'data'));
    }
}