<?php

namespace App\Http\Controllers;

use App\Project;
use function compact;
use Illuminate\Http\Request;
use function redirect;
use function response;

class UpdateProgressController extends Controller
{
    /**
     * @param Project $project
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    function show(Project $project)
    {
        $this->authorize('actual_resources', $project);

        $exporter = new \App\Export\ProjectProgressExport($project);
        $file = $exporter->handle();

        return response()->download($file, slug($project->name) . '_progress.xlsx')->deleteFileAfterSend(true);
    }

    /**
     * @param Project $project
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    function create(Project $project)
    {
        $this->authorize('actual_resources', $project);

        return view('project.update_progress', compact('project'));
    }

    /**
     * @param Project $project
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    function store(Project $project, Request $request)
    {
        $this->authorize('actual_resources', $project);

        $this->validate($request, ['file' => 'required|file|mimes:xls,xlsx']);

        $file = $request->file('file');
        $result = $this->dispatchNow(new \App\Jobs\UpdateProjectProgress($project, $file));

        if ($result['failed']) {
            return view('project.update_progress_failed', compact('project', 'result'));
        }

        flash("{$result['success']} Records have been imported", 'success');
        return redirect()->route('project.cost-control', $project);
    }

    function edit()
    {

    }

    function update()
    {

    }
}
