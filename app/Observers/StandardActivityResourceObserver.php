<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 20/12/16
 * Time: 10:09 ص
 */

namespace App\Observers;

use App\Breakdown;
use App\Productivity;
use App\StdActivityResource;

class StandardActivityResourceObserver
{
    function created(StdActivityResource $resource)
    {
        $template_id = $resource->template->id;
        Breakdown::with('wbs_level')
            ->where('template_id', $template_id)->get()
            ->each(function (Breakdown $breakdown) use ($resource) {
                $budget_qty = $breakdown->wbs_level->getBudgetQty($breakdown->cost_account);
                $eng_qty = $breakdown->wbs_level->getBudgetQty($breakdown->cost_account);
                $breakdown->resources()->create([
                    'std_activity_resource_id' => $resource->id,
                    'budget_qty' => $budget_qty,
                    'eng_qty' => $eng_qty,
                    'resource_waste' => $resource->resource_waste ?: 0,
                    'labor_count' => $resource->labor_count,
                    'remarks' => $resource->remarks,
                    'productivity_id' => $resource->productivity_id,
                    'resource_id' => $resource->resource_id
                ]);
            });
    }

    function updating(StdActivityResource $resource)
    {
        $resource->old_equation = $resource->getOriginal('equation');
    }

    function updated(StdActivityResource $resource)
    {
        if (isset($resource->template->project_id)) {
            $resource->updateShadows();
        }
    }

    function deleted()
    {

    }
}