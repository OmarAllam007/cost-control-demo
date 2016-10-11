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
        ini_set('max_execution_time', 300);
        $break_down_resources = $project->breakdown_resources()->get();
        $data = [];
        foreach ($break_down_resources as $break_down_resource) {
            $resource = $break_down_resource->resource->resource;
            $division = $break_down_resource->resource->resource->types;
            $root = $break_down_resource->resource->resource->types->root;
            if (!isset($data[ $root->name ])) {
                $data[ $root->name ] = [
                    'name' => $root->name,
                    'divisions' => [],
                ];
            }
            if (!isset($data[ $root->name ]['divisions'][ $division->id ])) {
                $data[ $root->name ]['divisions'][ $division->id ] = [
                    'name' => $division->name,
                    'resources' => [],
                ];
            }
            if (!isset($data[ $root->name ]['divisions'][ $division->id ]['resources'][ $resource->id ])) {

                $data[ $root->name ]['divisions'][ $division->id ]['resources'][ $resource->id ] = [
                    'code' => $resource->resource_code,
                    'name' => $resource->name,
                    'rate' => $resource->rate,
                    'unit' => isset(Unit::find($resource->unit)->type) ? Unit::find($resource->unit)->type : '',
                    'waste' => $resource->waste,
                    'partner' => isset(BusinessPartner::find($resource->business_partner_id)->name) ? BusinessPartner::find($resource->business_partner_id)->name : '',
                    'reference' => $resource->reference,
                    'budget_cost' => $break_down_resource->budget_cost,
                    'budget_unit' => $break_down_resource->budget_unit,
                ];
            }
        }
//        dd($data);
        ksort($data);

        return view('reports.resource_dictionary', compact('project', 'data'));
    }
}