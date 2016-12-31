<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 31/12/16
 * Time: 09:23 م
 */

namespace App\Observers;


use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BreakdownVariable;
use App\Formatters\BreakdownResourceFormatter;
use App\Survey;

class QSObserver
{
    function saving(Survey $survey)
    {
       $resources = BreakdownResource::whereHas('breakdown',function ($q) use($survey){
           if($survey->variables){
               foreach ($survey->variables as $variable){
                   $q->where('id',$variable->breakdown_id)->where('project_id',$survey->project_id);
               }
           }
       })->get();

        foreach ($resources as $breakdown_resource) {
            $formatter = new BreakdownResourceFormatter($breakdown_resource);
            BreakDownResourceShadow::where('breakdown_resource_id', $breakdown_resource->id)
                ->update($formatter->toArray());
        }
    }
}