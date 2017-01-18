<?php

namespace App\Policies;

use App\Project;
use App\ProjectUser;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    protected $project_user;

    //<editor-fold defaultstate="collapsed" desc="Budget Methods">
    function budget(User $user, Project $project)
    {
        return $this->canBudget($user, $project, __FUNCTION__);
    }

    function modify(User $user, Project $project)
    {
        return $project->owner_id == $user->id;
    }

    function resources(User $user, Project $project)
    {
        return $this->canBudget($user, $project, __FUNCTION__);
    }

    function wbs(User $user, Project $project)
    {
        return $this->canBudget($user, $project, __FUNCTION__);
    }

    function boq(User $user, Project $project)
    {
        return $this->canBudget($user, $project, __FUNCTION__);
    }

    function qty_survey(User $user, Project $project)
    {
        return $this->canBudget($user, $project, __FUNCTION__);
    }

    function breakdown(User $user, Project $project)
    {
        return $this->canBudget($user, $project, __FUNCTION__);
    }

    function productivity(User $user, Project $project)
    {
        return $this->canBudget($user, $project, __FUNCTION__);
    }

    function reports(User $user, Project $project)
    {
        return $this->canBudget($user, $project, __FUNCTION__) || $this->canCost($user, $project, __FUNCTION__);
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Cost Control Methods">
    function cost_control(User $user, Project $project)
    {
        return $this->canCost($user, $project, __FUNCTION__);
    }

    function cost_owner(User $user, Project $project)
    {
        return $user->id == $project->cost_owner_id;
    }

    function actual_resources(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function breakdown_templates(User $user, Project $project)
    {
        return $this->can($user, $project, __FUNCTION__);
    }

    function activity_mapping(User $user, Project $project)
    {
        return $this->canCost($user, $project, __FUNCTION__);
    }

    function resource_mapping(User $user, Project $project)
    {
        return $this->canCost($user, $project, __FUNCTION__);
    }

    function periods(User $user, Project $project)
    {
        return $this->canCost($user, $project, __FUNCTION__);
    }

    function remaining_unit_price(User $user, Project $project)
    {
        return $this->canCost($user, $project, __FUNCTION__);
    }

    function remaining_unit_qty(User $user, Project $project)
    {
        return $this->canCost($user, $project, __FUNCTION__);
    }

    function manual_edit(User $user, Project $project)
    {
        return $this->canCost($user, $project, __FUNCTION__);
    }

    function delete_resources(User $user, Project $project)
    {
        return $this->canCost($user, $project, __FUNCTION__);
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Helper methods">
    protected function canBudget(User $user, Project $project, $ability)
    {
        if ($project->owner_id === $user->id) {
            return true;
        }

        return $this->can($user, $project, $ability);
    }

    protected function canCost(User $user, Project $project, $ability)
    {
        if ($project->cost_owner_id === $user->id) {
            return true;
        }

        return $this->can($user, $project, $ability);
    }

    protected function can(User $user, Project $project, $ability)
    {
        $user = ProjectUser::where(['user_id' => $user->id, 'project_id' => $project->id])->first();
        if (!$user) {
            return false;
        }

        return $user->getAttribute($ability) == 1;
    }
    //</editor-fold>
}
