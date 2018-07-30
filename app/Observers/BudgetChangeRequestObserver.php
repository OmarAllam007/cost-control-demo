<?php

namespace App\Observers;

use App\BudgetChangeRequest;

class BudgetChangeRequestObserver
{
    function creating(BudgetChangeRequest $changeRequest)
    {
        $changeRequest->assigned_to = $changeRequest->project->owner_id;
    }

    function saved(BudgetChangeRequest $changeRequest)
    {
        if (!$changeRequest->wasChanged('assigned_to')) {
            return true;
        }
    }
}