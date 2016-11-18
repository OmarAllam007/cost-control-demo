<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 4:25 PM
 */

namespace App\Http\Controllers\Reports;


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
        $break_down_resources = $project->breakdown_resources()->get();
        $data = [];
        $parents = [];


        foreach ($break_down_resources as $break_down_resource) {
            $resource = $break_down_resource->resource;
            $division = $break_down_resource->resource->types;
            $root = $break_down_resource->resource->types->root;
            if (!isset($data[$root->name])) {
                $data[$root->name] = [
                    'name' => $root->name,
                    'divisions' => [],
                ];
            }


            if (!isset($data[$root->name]['divisions'][$division->id])) {
                $data[$root->name]['divisions'][$division->id] = [
                    'name' => $division->name,
                    'parents' => [],
                    'resources' => [],
                ];
            }
            $parent = $division;
            while ($parent->parent) {
                $parent = $parent->parent;
                if ($parent->name == $division->root->name) {
                    continue;
                }
                if (!isset($data[$root->name]['divisions'][$division->id]['parents'][$parent->id])) {
                    $data[$root->name]['divisions'][$division->id]['parents'][$parent->id] = [
                        'name' => $parent->name
                    ];
                }

            }

            $latest_resource = Resources::orderBy('created_at', 'desc')->where('resource_id', $resource->id)->get()->first();
            if (!isset($data[$root->name]['divisions'][$division->id]['resources'][$resource->id])) {
                $data[$root->name]['divisions'][$division->id]['resources'][$resource->id] = [
                    'code' => $resource->resource_code,
                    'name' => $resource->name,
                    'rate' => !is_null($latest_resource) ? $latest_resource->rate : $resource->rate,
                    'unit' => isset(Unit::find($resource->unit)->type) ? Unit::find($resource->unit)->type : '',
                    'waste' => $resource->waste,
                    'partner' => isset(BusinessPartner::find($resource->business_partner_id)->name) ? BusinessPartner::find($resource->business_partner_id)->name : '',
                    'reference' => $resource->reference,
                    'budget_cost' => 0,
                    'budget_unit' => 0,
                ];


            }

            $data[$root->name]['divisions'][$division->id]['resources'][$resource->id]['budget_cost'] += $break_down_resource->budget_cost;
            $data[$root->name]['divisions'][$division->id]['resources'][$resource->id]['budget_unit'] += $break_down_resource->budget_unit;
        }

        //algorithms for delete duplicate data
        foreach ($data as $key => $value) {
            foreach ($value['divisions'] as $dKey => $dValue) {
                foreach ($dValue['parents'] as $pKey => $parent) {
                    if (!in_array($parent['name'], $parents)) {
                        $parents[] = $parent['name'];
//
                    }
                    else{
                        unset($data[$key]['divisions'][$dKey]['parents'][$pKey]);
                    }

                }
            }
        }

        ksort($data);

        return view('reports.resource_dictionary', compact('project', 'data'));
    }
}