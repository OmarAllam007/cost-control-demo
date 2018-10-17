<?php

namespace App\Policies;

use App\BudgetChangeRequest;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChangeRequestPolicy
{
    use HandlesAuthorization;


    protected function close_request(User $user, BudgetChangeRequest $changeRequest)
    {
        return in_array($user->id, [$changeRequest->project->owner->id, $changeRequest->assigned_to]);
    }

    protected function reassigned_request(User $user, BudgetChangeRequest $changeRequest)
    {
        return $user->id == $changeRequest->project->owner->id;
    }


}
