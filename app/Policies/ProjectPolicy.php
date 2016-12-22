<?php

namespace App\Policies;

use App\Project;
use App\ProjectUser;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /** @var ProjectUser */
    protected $project_user;

    function budget(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function cost_control(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function modify(User $user, Project $project)
    {
        return $project->owner_id == $user->id;
    }

    function resources(User $user, Project $project)
    {
        
        return $this->can($user, $project, __FUNCTION__);
    }

    function wbs(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function boq(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function qty_survey(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function breakdown(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function productivity(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function reports(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function actual_resources(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function breakdown_templates(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    protected function can(User $user, Project $project, $ability)
    {
        if ($project->owner_id === $user->id) {
            return true;
        }

        if (!$this->project_user) {
            $this->project_user = ProjectUser::where(['user_id' => $user->id, 'project_id' => $project->id])->first();
            if (!$this->project_user) {
                return false;
            }
        }

        return $this->project_user->getAttribute($ability) == 1;
    }
}
