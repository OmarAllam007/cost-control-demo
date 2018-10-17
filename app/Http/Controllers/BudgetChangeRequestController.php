<?php

namespace App\Http\Controllers;

use App\BudgetChangeRequest;
use App\Events\ChangeRequestClosed;
use App\Events\ChangeRequestCreated;
use App\Events\ChangeRequestReassign;
use App\Project;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

use App\Http\Requests;

class BudgetChangeRequestController extends Controller
{
    public function index(Project $project)
    {
        if (!can('budget', $project) && !can('cost_control', $project)) {
            throw new AuthorizationException("You are not authorized to do this action");
        }
        $requests = $project->requests->each(function (BudgetChangeRequest $request) {
            $request['destory'] = route('change-request.destory', $request);
            $request['created_by'] = $request->created_by()->first()->name ?? '';
            $request['assigned_to'] = $request->assigned_to()->first()->name ?? '';
            $request['closed_by'] = $request->closed_by()->first()->name ?? '';
        });

        return $requests;
//        return BudgetChangeRequest::where('project_id', $project->id)->paginate();
    }

    public function create(Project $project)
    {
        if (!can('budget', $project) && !can('cost_control', $project)) {
            throw new AuthorizationException("You are not authorized to do this action");
        }

        return view('change-request.create', compact('project'));
    }

    public function store(Request $changeRequest, Project $project)
    {
        if (!can('budget', $project) && !can('cost_control', $project)) {
            throw new AuthorizationException("You are not authorized to do this action");
        }

        $data = $changeRequest->except('iframe');

        $data['project_id'] = $project->id;
        $data['assigned_to'] = $project->owner_id;
        $data['created_by'] = \Auth::id();

        $changeRequest = BudgetChangeRequest::create($data);

        event(new ChangeRequestCreated($changeRequest));

        return redirect()->to('blank?reload=change_request');
    }

    public function show(Project $project, BudgetChangeRequest $changeRequest)
    {
        if (!can('budget', $changeRequest->project) && !can('cost_control', $changeRequest->project)) {
            throw new AuthorizationException("You are not authorized to do this action");
        }

        return view('change-request.show', compact('changeRequest'));
    }

    public function update(Request $request, $id)
    {

    }

    public function destory(BudgetChangeRequest $request)
    {
        $request->delete();
        return redirect()->back();
    }


    public function reassign(Project $project, BudgetChangeRequest $changeRequest)
    {

        $this->validate(\request(), ['due_date' => 'required', 'assigned_to' => 'required']);

        $changeRequest->update([
            'assigned_to' => \request()->assigned_to,
            'due_date' => \request()->due_date ?? '',
        ]);

        event(new ChangeRequestReassign($changeRequest));

        return redirect()->back();

    }

    public function close(Project $project, BudgetChangeRequest $changeRequest, Request $request)
    {
        $changeRequest->update([
            'close_note' => $request->close_note ?? '',
            'closed_by' => \Auth::id(),
            'closed_at' => Carbon::now(),
            'closed' => 1,
        ]);

        event(new ChangeRequestClosed($changeRequest));

        return redirect()->route('project.budget', compact('project'));
    }
}
