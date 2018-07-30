<?php

namespace App\Http\Controllers;

use App\BudgetChangeRequest;
use App\Project;
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

        return BudgetChangeRequest::where('project_id', $project->id)->paginate();
    }

    public function create(Project $project)
    {
        if (!can('budget', $project) && !can('cost_control', $project)) {
            throw new AuthorizationException("You are not authorized to do this action");
        }

        return view('change-request.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        if (!can('budget', $project) && !can('cost_control', $project)) {
            throw new AuthorizationException("You are not authorized to do this action");
        }

        $data = $request->all();
        $data['project_id'] = $project->id;
        BudgetChangeRequest::create($data);

        return redirect()->to('blank?reload=change-request');
    }

    public function show(BudgetChangeRequest $changeRequest)
    {
        if (!can('budget', $changeRequest->project) && !can('cost_control', $changeRequest->project)) {
            throw new AuthorizationException("You are not authorized to do this action");
        }

        return view('change-request.show', compact('changeRequest'));
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
