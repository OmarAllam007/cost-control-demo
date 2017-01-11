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
    //todo: remove this class at all as there is another class QuantitySurvey Observer!!!!
//    function updated(Survey $survey)
//    {
//        $survey->updateBreakdownResources();
//    }
}