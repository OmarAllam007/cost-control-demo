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

class HighPriorityMaterials
{
    public function getTopHighPriorityMaterials(Project $project)
    {
        $break_down_resources = $project->breakdown_resources()->get();
        foreach ($break_down_resources as $break_down_resource) {
            $resource = $break_down_resource->resource->resource;
            $root = $break_down_resource->resource->resource->types->root;
            $resource_type = $break_down_resource->resource->resource->types;

            if ($root->name == '03.MATERIAL') {
                if (!isset($data[ $resource_type->name ])) {
                    $data[ $resource_type->name ] = [
                        'name' => $resource_type->name,
                        'budget_cost' => 0,
                        'budget_unit' => 0,
                        'unit' => '',
                    ];
                }

                $data[ $resource_type->name ]['budget_cost'] += $break_down_resource->budget_cost;
                $data[ $resource_type->name ]['unit'] = Unit::find($resource->unit)->type;
                $data[ $resource_type->name ]['budget_unit'] += $break_down_resource->budget_unit;
            }


        }
        if($data)
        {
            usort($data, function ($a, $b) {
                return $b['budget_cost'] - $a['budget_cost'];
            });
        }

        return view('reports.high_priority_materials',compact('data', 'project'));
    }
}