<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 14/12/16
 * Time: 10:58 ص
 */

namespace App\Observers;


use App\BreakDownResourceShadow;
use App\BreakdownTemplate;

class BreakdownTemplateObserver
{
    function updated(BreakdownTemplate $template)
    {
        if ($template->project_id) {
            BreakDownResourceShadow::where('template_id', $template->id)->update(['template' => $template->name]);
        } else {
            $project_template_ids = BreakdownTemplate::where('parent_template_id', $template->id)->pluck('id');

            BreakdownTemplate::whereIn('id', $project_template_ids)->update(['name' => $template->name, 'code' => $template->code]);
            BreakDownResourceShadow::whereIn('template_id', $project_template_ids)->update(['template' => $template->name]);
        }
    }
}