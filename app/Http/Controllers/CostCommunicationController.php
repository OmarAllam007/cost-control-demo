<?php

namespace App\Http\Controllers;

use App\CommunicationSchedule;
use App\CommunicationUser;
use App\Jobs\SendCommunicationPlan;
use App\Project;
use App\ProjectRole;
use App\Role;
use Illuminate\Http\Request;

class CostCommunicationController extends Controller
{
    function create(Project $project)
    {
        if (cannot('cost_owner', $project)) {
            flash('You are not authorized to do this action');
            return redirect()->route('project.cost-control', $project);
        }

        $project_roles = ProjectRole::where('project_id', $project->id)->with('role', 'role.reports')->get()->groupBy('role_id');
        $roles = Role::all()->keyBy('id');
        $periods = $project->periods()->readyForReporting()->get();

        return view('communication.cost-control', compact('project', 'project_roles', 'roles', 'periods'));
    }

    function store(Project $project, Request $request)
    {
        if (cannot('cost_owner', $project)) {
            flash('You are not authorized to do this action');
            return redirect()->route('project.cost-control', $project);
        }

        $schedule = CommunicationSchedule::create(['project_id' => $project->id, 'period_id' => $request->period_id, 'type' => 'Cost Control']);
        foreach ($request->schedule as $role_id => $data) {
            if ($data['enabled']) {
                $user_ids = array_filter($data['users']);
                $report_ids = array_filter($data['reports']);
                foreach ($user_ids as $user_id) {
                    $user = $schedule->users()->create(compact('user_id', 'role_id'));
                    foreach ($report_ids as $report_id) {
                        $user->reports()->create(compact('report_id'));
                    }
                }
            }
        }

        $job = new SendCommunicationPlan($schedule);
        $job->onQueue('raci');
        $this->dispatch($job);

        flash('Sending reports has been scheduled', 'info');

        if ($request->exists('iframe')) {
            return redirect('/blank');
        }

        return redirect()->route('project.budget', $project);
    }
}
