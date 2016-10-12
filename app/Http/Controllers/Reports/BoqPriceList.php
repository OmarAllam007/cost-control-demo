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
            $std_resources = $breakDown_resource->resource;
            $resources = $std_resources->resource;
            $break_down = $breakDown_resource->breakdown;
            $root = $resources->types->root;
            $wbs_level = $break_down->wbs_level;
            $cost_account = $break_down->cost_account;
            $boqs = Boq::where('cost_account', $cost_account)->get();


            if (!isset($data[ $wbs_level->name ])) {
                $data[ $wbs_level->name ] = [
                    'name' => $wbs_level->name,
                    'items' => [],
                    'totals'=>[
                        'LABORS' => 0,
                        'MATERIAL' => 0,
                        'Subcontractors' => 0,
                        'EQUIPMENT' => 0,
                        'SCAFFOLDING' => 0,
                        'OTHERS' => 0,
                    ],
                ];

            }
            foreach ($boqs as $boq) {
                if (!isset($data[ $wbs_level->name ]['items'][ $boq->description ])) {
                    $data[ $wbs_level->name ]['items'][ $boq->description ] = [
                        'id'=>$boq->id,
                        'boq_name' => $boq->description,
                        'cost_accounts' => [],
                    ];
                }
                if (!isset($data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ])) {
                    $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ] = [
                        'cost_account' => $cost_account,
                        'LABORS' => 0,
                        'MATERIAL' => 0,
                        'Subcontractors' => 0,
                        'EQUIPMENT' => 0,
                        'SCAFFOLDING' => 0,
                        'OTHERS' => 0,
                        'total_resources'=>0
                    ];
                    if($root->id==16){
                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['LABORS']=$breakDown_resource->boq_unit_rate;

                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['total_resources']=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['LABORS'];

                        $data[ $wbs_level->name ]['totals']['LABORS']+=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['LABORS'];
                    }
                    else if($root->id==22){
                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['EQUIPMENT']=$breakDown_resource->boq_unit_rate;

                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['total_resources']=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['EQUIPMENT'];

                        $data[ $wbs_level->name ]['totals']['EQUIPMENT']+=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['EQUIPMENT'];

                    }
                    else if($root->id==43){
                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['Subcontractors']=$breakDown_resource->boq_unit_rate;

                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['total_resources']=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['Subcontractors'];

                        $data[ $wbs_level->name ]['totals']['Subcontractors']+=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['Subcontractors'];

                    }
                    else if($root->id==136){
                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['MATERIAL']=$breakDown_resource->boq_unit_rate;

                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['total_resources']=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['MATERIAL'];

                        $data[ $wbs_level->name ]['totals']['MATERIAL']+= $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['MATERIAL'];
                    }
                    else if($root->id==230){
                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['OTHERS']=$breakDown_resource->boq_unit_rate;

                        $data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['total_resources']=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['OTHERS'];

                        $data[ $wbs_level->name ]['totals']['OTHERS']+=$data[ $wbs_level->name ]['items'][ $boq->description ]['cost_accounts'][ $cost_account ]['OTHERS'];

                    }
                }
            }


        }

        return view('reports.boq_price_list', compact('project','data'));
    }
}