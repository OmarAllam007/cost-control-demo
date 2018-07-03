<?php

namespace App\Http\Controllers;

use App\BreakDownResourceShadow;
use App\Project;
use function compact;
use function flash;
use Illuminate\Http\Request;
use function redirect;
use function response;

class UpdateProgressController extends Controller
{
    /**
     * @param Project $project
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \PHPExcel_Exception
     */
    function show(Project $project)
    {
        $this->authorize('actual_resources', $project);

        $exporter = new \App\Export\ProjectProgressExport($project);
        $file = $exporter->handle();

        return response()
            ->download($file, slug($project->name) . '_progress.xlsx')
            ->deleteFileAfterSend(true);
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
        $this->dispatchNow(new \App\Jobs\UpdateProjectProgress($project, $file));

        return redirect()->route('project.modify-progress', $project);
    }

    /**
     * @param Project $project
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    function edit(Project $project)
    {
        $this->authorize('actual_resources', $project);
        if (!\Cache::has('update_progress_' . $project->id)) {
            flash('Please upload progress file');
            return redirect()->route('project.update-progress', $project);
        }

        $activityProgress = \Cache::get("update_progress_{$project->id}");
        return view('project.modify-progress', compact('activityProgress', 'project'));
    }

    /**
     * @param Project $project
     * @param Request $request
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    function update(Project $project, Request $request)
    {
        $this->authorize('actual_resources', $project);

        $this->validate($request, ['progress.*' => 'gt:0|lte:100']);

        foreach ($request->get('progress') as $id => $progress) {
            $status = 'In Progress';
            if ($progress == 100) {
                $status = 'Closed';
            }

            $project->shadows()->where('id', $id)->update(['progress' => $progress, 'status' => $status]);
        }

        \Cache::forget("update_progress_{$project->id}");
        flash('Progress has been updated', 'success');
        return redirect()->route('project.cost-control', $project);
    }
}
