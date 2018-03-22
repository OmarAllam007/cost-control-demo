<?php

namespace App\Observers;

use App\ProjectRole;
use App\Role;
use App\RoleReport;

class RoleObserver
{
    function deleted(Role $role)
    {
        RoleReport::where('role_id', $role->id)->delete();
        ProjectRole::where('role_id', $role->id)->delete();
    }
}