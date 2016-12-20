<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 14/12/16
 * Time: 10:58 ص
 */

namespace App\Observers;


use App\BreakdownTemplate;

class BreakdownTemplateObserver
{
    function creating(BreakdownTemplate $template){
        $names = explode('»', $template->activity->division->path);

        $code = [];
        foreach ($names as $name) {
            $name = trim($name);
            $code[] = substr(trim($name), 0, 3);
            if (strrchr($name, ' ')) {
                $position = strrpos($name, ' ');
                $code[] = substr($name, $position + 1, 1);
            }
            $code[] = '.';
        }

        $activityName = $template->activity->name;
        $code = implode('', $code) . substr($activityName, 0, 3);
        $num = 1;
        $item = BreakdownTemplate::where('code', 'like', $code . '%')->get(['code'])->last();

        if (!is_null($item)) {
            $itemCode = substr($item->code, strrpos($item->code, '.') + 1);
            $itemCode++;
            $code = $code .'.'. $itemCode;
            $template->code = $code;
        } else {
            $template->code = $code .'.'. $num;
        }

    }
    function created(BreakdownTemplate $template){

    }
    function saving(BreakdownTemplate $template){
    }
    function deleted(BreakdownTemplate $template){

    }

}