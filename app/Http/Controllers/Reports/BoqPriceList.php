<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/11/2016
 * Time: 2:53 PM
 */

namespace App\Http\Controllers\Reports;


use App\Boq;
use App\Project;

class BoqPriceList
{
    public function getBoqPriceList(Project $project)
    {
        $breakDown_resources = $project->breakdown_resources()->with('breakdown.resources','breakdown.wbs_level','template_resource','template_resource.resource')->get();
        $data = [];
        foreach ($breakDown_resources as $breakDown_resource) {
            $resource = $breakDown_resource->resource;
            $root = $resource->types->root;
            $wbs_level = $breakDown_resource->breakdown->wbs_level;
            $cost_account = $breakDown_resource->breakdown->cost_account;
            $boq = Boq::where('cost_account', $cost_account)->first();
            if (!isset($data[ $wbs_level->name ])) {
                $data[ $wbs_level->name ] = [
                    'name' => $wbs_level->name,
                    'items' => [],
                ];

            }
            if (!isset($data[ $wbs_level->name ]['items'][ $cost_account ])) {
                $data[ $wbs_level->name ]['items'][ $cost_account ] = [
                    'id' => $boq->id,
                    'boq_name' => $boq->description,
                    'cost_account' => $cost_account,
                    'LABORS' => 0,
                    'MATERIAL' => 0,
                    'Subcontractors' => 0,
                    'EQUIPMENT' => 0,
                    'SCAFFOLDING' => 0,
                    'OTHERS' => 0,
                    'total_resources' => 0,
                ];
            }

            $name = substr($root->name, strpos($root->name, '.') + 1);

            $data[ $wbs_level->name ]['items'][ $cost_account ][$name] += $breakDown_resource->boq_unit_rate;
            $data[ $wbs_level->name ]['items'][ $cost_account ]['total_resources'] += $breakDown_resource->boq_unit_rate;


        }
        return view('reports.boq_price_list', compact('project', 'data'));
    }


}
