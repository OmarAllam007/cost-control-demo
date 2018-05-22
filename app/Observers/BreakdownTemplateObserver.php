<?php

namespace App\Observers;

use App\BreakDownResourceShadow;
use App\BreakdownTemplate;

class BreakdownTemplateObserver
{
    function creating(BreakdownTemplate $template)
    {
        if (!$template->parent_template_id) {
            $template->code = $this->generateCode($template);
        }
    }

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

    private function generateCode(BreakdownTemplate $template)
    {
        $max_code = BreakdownTemplate::where('std_activity_id', $template->std_activity_id)->max('code');

        $last_part = intval(collect(explode('.', $max_code))->last()) + 1;

        return $template->activity->code . '.' . sprintf("%03d", $last_part);
    }
}