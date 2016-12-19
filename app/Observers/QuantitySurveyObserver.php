<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 19/12/16
 * Time: 01:08 م
 */

namespace App\Observers;


use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Survey;
use Illuminate\Database\Eloquent\Builder;

class QuantitySurveyObserver
{

    function updated(Survey $quantitySurvey)
    {

        $ids = BreakDownResourceShadow::where('project_id', $quantitySurvey->project_id)->
        where('cost_account', $quantitySurvey->cost_account)->pluck('breakdown_resource_id');
        BreakdownResource::whereIn('id', $ids)->update(['budget_qty' => $quantitySurvey->budget_qty, 'eng_qty' => $quantitySurvey->eng_qty]);
//        foreach ($resources as $resource){
//            BreakDownResourceShadow::where('breakdown_resource_id',$resource)->update(['budget_qty' => $quantitySurvey->budget_qty, 'eng_qty' => $quantitySurvey->eng_qty]);
//        }
//        Breakdown::where('project_id', $quantitySurvey->project_id)->where('cost_account', $quantitySurvey->cost_account)->first();
//
        //todo to be remember this query
//        BreakdownResource::whereHas('breakdown', function(Builder $q)  use ($quantitySurvey) {
//            $q->where('project_id', $quantitySurvey->project_id)->where('cost_account', $quantitySurvey->cost_account)->get();
//        })->update(['budget_qty' => $quantitySurvey->budget_qty, 'eng_qty' => $quantitySurvey->eng_qty]);
    }
}