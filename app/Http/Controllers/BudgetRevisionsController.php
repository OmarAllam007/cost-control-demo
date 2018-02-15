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

        if ($request->exists('iframe')) {
            return redirect()->to('/blank?reload=revisions');
        }

        return redirect()->to(route('project.budget', $project));
    }

    function show(Project $project, BudgetRevision $revision)
    {
        /** @var BudgetRevision $rev1 */
        $rev1 = $project->revisions()->orderBy('id')->first();

        $firstRevision = $rev1->statsByDiscipline();
        $thisRevision = $revision->statsByDiscipline();
        $disciplines = $thisRevision->keys();

        $firstRevisionTotal = $rev1->total();
        $thisRevisionTotal = $revision->total();
        $diffTotal = $thisRevisionTotal - $firstRevisionTotal;
        $diffPercentTotal = $firstRevisionTotal ? ($diffTotal/$firstRevisionTotal) * 100 : 0;

        return view('revisions.show', compact(
            'project', 'revision', 'firstRevision', 'thisRevision', 'rev1', 'disciplines',
            'firstRevisionTotal', 'thisRevisionTotal', 'diffTotal', 'diffPercentTotal'
        ));
    }

    function export(Project $project, BudgetRevision $revision) {
        $file = dispatch(new ExportRevisionJob($revision));

        return \Response::download($file)->deleteFileAfterSend(true);
    }

    function edit(Project $project, BudgetRevision $revision)
    {
        $project = $revision->project;
        if (cannot('budget_owner', $project)) {
            flash('You are not authorized to do this action');
            return redirect()->route('project.budget', $project);
        }

        return view('revisions.edit', compact('revision', 'project'));
    }

    function update(Request $request, $project, BudgetRevision $revision)
    {
        $project = $revision->project;

        if (cannot('modify', $project)) {
            flash('You are not authorized to do this action');
            return redirect()->route('project.budget', $project);
        }

        $this->validate($request, ['name' => 'required']);

        $revision->update($request->only('name', 'change_order_amount', 'original_contract_amount'));

        flash('Revision has been updated', 'success');
        return redirect()->to(route('project.budget', $project));
    }

    function destroy(Project $project, BudgetRevision $revision)
    {
        if (cannot('budget_owner', $project)) {
            flash('You are not authorized to do this action');
        } elseif ($revision->is_automatic) {
            flash('Cannot delete automatic revisions');
        } else {
            $revision->delete();
            flash('Revision has been deleted', 'info');
        }

        return redirect()->route('project.budget', $project);

    }
}
