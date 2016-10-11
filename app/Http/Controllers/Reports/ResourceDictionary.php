<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/10/2016
 * Time: 4:25 PM
 */

namespace App\Http\Controllers\Reports;


use App\Project;
use App\Resources;
use App\ResourceType;
use Make\Makers\Resource;

class ResourceDictionary
{
    public function getResourceDictionary(Project $project)
    {
        ini_set('max_execution_time', 300);
        $break_downs = $project->breakdowns()->get();
        $data = [];
        foreach ($break_downs as $break_down) {
            foreach ($break_down->template->resources as $resource) {
                $root = $resource->resource->types->root;
                if (!isset($data[ $root->name ])) {
                    $data[ $root->name ] = [
                        'divisions' => ResourceType::where('parent_id', $root->id)->get(),
                        'resources' => [],
                        'budget_cost' => [],
                        'budget_unit' => [],

                    ];
                }

                foreach ($data[ $root->name ]['divisions'] as $division) {
                    $data[ $root->name ]['resources'][ $division->id ] = $division->resources;
                }
            }



        }
//        foreach ($project->breakdown_resources as $br_resource) {
//            foreach ($data as $key=>$value){
//                foreach ($data[ $key ]['divisions'] as $division) {
//                    foreach ($data[ $key ]['resources'][ $division->id ] as $resourcee) {
//                        $data[ $key ]['budget_cost'][ $resourcee->id ][] = $br_resource->budget_cost;
//                    }
//                }
//            }
//        }
        ksort($data);
        dd($data);
        return view('reports.resource_dictionary', compact('project', 'data'));
    }
}