<?php

namespace App\Policies;

use App\ModulesUsers;
use App\User;

class DataPolicy
{
    protected $modules = [
        'resources' => 1,
        'std-activities' => 2,
        'std-activity' => 2,
        'breakdown-template' => 3,
        'breakdown-templates' => 3,
        'productivity' => 4
    ];

    function read(User $user, $module)
    {
        return $this->can($user, $module, 'read');
    }

    function write(User $user, $module)
    {
        return $this->can($user, $module, 'write');
    }

    function delete(User $user, $module)
    {
        return $this->can($user, $module, 'delete');
    }

    protected function can($user, $module, $ability)
    {
        $module = is_int($module)? $module : $this->modules[$module];

        return ModulesUsers::where('user_id', $user->id)
            ->where('module_id', $module)
            ->where($ability, true)
            ->exists();
    }

}
