<?php

namespace App\Http\Controllers;

use App\BudgetRevision;
use App\Jobs\ExportRevisionJob;
use App\Project;
use Illuminate\Http\Request;


class BudgetRevisionsController extends Controller
{
    function index(Project $project)
    {
        return ['revisions' => $project->revisions, 'can_edit' => can('modify', $project)];
    }

    function create(Project $project)
    {
        if (cannot('modify', $project)) {
            flash('You are not authorized to do this action');
            return redirect()->route('project.budget', $project);
        }

        $revision = new BudgetRevision();
        $revision->project_id = $project->id;
        $latest = $project->revisions()->latest()->first();
        $revision->rev_num = $latest ? $latest->rev_num + 1 : 1;
        return view('revisions.create', compact('revision', 'project'));
    }

    function store(Request $request, Project $project)
    {
        if (cannot('modify', $project)) {
            flash('You are not authorized to do this action');
            return redirect()->route('project.budget', $project);
        }

        $this->validate($request, ['name' => 'required']);
        $project->revisions()->create($request->only('name'));

        flash('Revision has been created');
        return redirect()->to(route('project.budget', $project));
    }

    function show(Project $project, BudgetRevision $revision)
    {
        return view('revisions.show', compact('project', 'revision'));
    }

    function export(Project $project, BudgetRevision $revision) {
        $file = dispatch(new ExportRevisionJob($revision));

        return \Response::download($file)->deleteFileAfterSend(true);
    }

    function edit(BudgetRevision $revision)
    {
        $project = $revision->project;
        if (cannot('modify', $project)) {
            flash('You are not authorized to do this action');
            return redirect()->route('project.budget', $project);
        }

        return view('revisions.edit', compact('revision', 'project'));
    }

    function update(Request $request, BudgetRevision $revision)
    {
        $project = $revision->project;

        if (cannot('modify', $project)) {
            flash('You are not authorized to do this action');
            return redirect()->route('project.budget', $project);
        }

        $this->validate($request, ['name' => 'required']);

        $revision->update($request->only('name'));

        flash('Revision has been updated');
        return redirect()->to(route('project.budget', $project));
    }

    /*function destroy(BudgetRevision $revision)
    {

    }*/
}
