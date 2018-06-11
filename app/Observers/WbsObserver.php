<?php

namespace App\Observers;

use App\ActivityMap;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Jobs\CacheWBSTreeInQueue;
use App\WbsLevel;

class WbsObserver
{
    protected $updateCode = false;

    protected $old_code = '';

    function creating(WbsLevel $level)
    {
        if (!$level->sap_code) {
            if ($level->parent) {
                $maxCode = $level->parent->children()->max('sap_code');
                $partial = 1;
                if ($maxCode) {
                    $partial = collect(explode('.', $maxCode))->last() + 1;
                }

                $level->sap_code = $level->parent->sap_code . '.' . sprintf('%02d', $partial);
            } else {
                $level->sap_code = $level->project->code;
            }
        }
    }

    function created(WbsLevel $wbs)
    {
        dispatch(new CacheWBSTreeInQueue($wbs->project));
    }

    function updating(WbsLevel $wbs)
    {
        $this->updateCode = $wbs->isDirty('code');
        $this->old_code = $wbs->code;
    }

    function updated(WbsLevel $wbs)
    {
        dispatch(new CacheWBSTreeInQueue($wbs->project));
        if ($this->updateCode) {
            $exists = ActivityMap::where('activity_code', $this->old_code)->where('project_id', $wbs->project)->exists();
            if (!$exists) {
                BreakdownResource::flushEventListeners();
                BreakDownResourceShadow::with('std_activity')->with('breakdown_resource')->where('wbs_id', $wbs->id)->get()
                    ->each(function(BreakDownResourceShadow $shadow) use ($wbs) {
                        $code = $wbs->code . $shadow->std_activity->id_partial;
                        $shadow->update(compact('code'));
                        $shadow->breakdown_resource->update(compact('code'));
                    });
            }
        }
    }
}