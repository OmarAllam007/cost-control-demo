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
        $breakDown_resources = $project->breakdown_resources()->get();
        $data = [];
        foreach ($breakDown_resources as $breakDown_resource) {
            $resources = $breakDown_resource->resource->resource;
            $root = $resources->types->root;
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
            $data = $this->getResourceTypeQuantity($data, $breakDown_resource,$wbs_level,$cost_account, $name);

        }
        return view('reports.boq_price_list', compact('project', 'data'));
    }

    public function getResourceTypeQuantity($data, $breakDown_resource,$wbs_level,$cost_account, $name)
    {
        if ($name == 'LABORS') {
            $data[ $wbs_level->name ]['items'][ $cost_account ]['LABORS'] += $breakDown_resource->project_resource->rate;
            $data[ $wbs_level->name ]['items'][ $cost_account ]['total_resources'] += $breakDown_resource->project_resource->rate;
        }
        if ($name == 'EQUIPMENT') {
            $data[ $wbs_level->name ]['items'][ $cost_account ]['EQUIPMENT'] += $breakDown_resource->project_resource->rate;
            $data[ $wbs_level->name ]['items'][ $cost_account ]['total_resources'] += $breakDown_resource->project_resource->rate;
        }
        if ($name == 'MATERIAL') {
            $data[ $wbs_level->name ]['items'][ $cost_account ]['MATERIAL'] += $breakDown_resource->project_resource->rate;
            $data[ $wbs_level->name ]['items'][ $cost_account ]['total_resources'] += $breakDown_resource->project_resource->rate;
        }
        if ($name == 'Subcontractors') {
            $data[ $wbs_level->name ]['items'][ $cost_account ]['Subcontractors'] += $breakDown_resource->project_resource->rate;
            $data[ $wbs_level->name ]['items'][ $cost_account ]['total_resources'] += $breakDown_resource->project_resource->rate;
        }
        if ($name == 'SCAFFOLDING') {
            $data[ $wbs_level->name ]['items'][ $cost_account ]['SCAFFOLDING'] += $breakDown_resource->project_resource->rate;
            $data[ $wbs_level->name ]['items'][ $cost_account ]['total_resources'] += $breakDown_resource->project_resource->rate;
        }
        if ($name == 'OTHERS') {
            $data[ $wbs_level->name ]['items'][ $cost_account ]['OTHERS'] += $breakDown_resource->project_resource->rate;
            $data[ $wbs_level->name ]['items'][ $cost_account ]['total_resources'] += $breakDown_resource->project_resource->rate;
        }

        return $data;
    }
}
