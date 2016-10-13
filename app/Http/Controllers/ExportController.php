<?php
/**
 * Created by PhpStorm.
 * User: omar.garana
 * Date: 10/12/2016
 * Time: 3:56 PM
 */

namespace App\Http\Controllers;


use App\Project;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportQuantitySurvey(Request $request, Project $project)
    {
        $break_down_resources = $project->breakdown_resources()->get();
        $excelObj = new  \PHPExcel();
        $quantity_survey = ['Cost Account'=>[],
            'WBS-Level'=>[],
            'Description'=>[],
            'Budget Quantity'=>[],
            'Eng Quantity'=>[],
        ];
//        dd($quantity_survey);
        foreach ($break_down_resources as $break_down_resource) {
//            $quantity_survey['Cost Account'][] = $break_down_resource->breakdown->cost_account;
            $quantity_survey['WBS-Level'][] = $break_down_resource->breakdown->wbs_level->name;
            $quantity_survey['Budget Quantity'][]=$break_down_resource->productivity->budget_qty;
            $quantity_survey['Eng Quantity'][] = $break_down_resource->productivity->eng_qty;
        }
        dd($quantity_survey );
    }
}