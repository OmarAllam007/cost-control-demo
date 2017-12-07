<?php

namespace App;

use App\Behaviors\HasChangeLog;
use App\Behaviors\RecordsUser;
use Illuminate\Database\Eloquent\Model;

class ProjectRole extends Model
{
    use HasChangeLog, RecordsUser;

    public static function updateRoles(Project $project, $input)
    {
        $roles = collect();
        foreach($input as $role) {
            foreach ($role['users'] as $user) {
                $user['role_id'] = $role['role_id'];
                $roles->push($project->roles()->create($user));
            }
        }

        return $roles;
    }
}
