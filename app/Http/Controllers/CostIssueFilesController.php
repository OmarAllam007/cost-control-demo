<?php

namespace App\Http\Controllers;

use App\CostIssueFile;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CostIssueFilesController extends Controller
{
    public function index(Project $project)
    {
        $issueFiles = CostIssueFile::whereProjectId($project->id)->paginate();

        return view('cost_issue_files.index', compact('issueFiles', 'project'));
    }

    public function create(Project $project)
    {
        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        return view('cost_issue_files.create', compact('project','periods'));
    }

    public function store(Request $request, Project $project)
    {
        $this->validate($request, [
            'subject' => 'required', 'period_id' => 'required', 'file' => 'required|file|mimes:xls,xlsx|max:2048'
        ]);

        $cost_issue_file = new CostIssueFile($request->only('subject', 'period_id', 'note'));
        $cost_issue_file->user_id = auth()->id();
        $cost_issue_file->project_id = $project->id;
        $cost_issue_file->file = $this->uploadFile($cost_issue_file, $request);
        $cost_issue_file->save();

        flash('File has been saved', 'success');
        return redirect('project/' . $cost_issue_file->project_id . '/issue-files');
    }

    public function show($project, CostIssueFile $cost_issue_file)
    {
        return \Response::download($cost_issue_file->file_path, $cost_issue_file->file_name);
    }

    public function edit(Project $project, CostIssueFile $cost_issue_file)
    {
        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        return view('cost_issue_files.edit', compact('periods', 'cost_issue_file', 'project'));
    }

    public function update(Request $request, $project, CostIssueFile $cost_issue_file)
    {
        if (!can('cost_owner', $cost_issue_file->project) && auth()->id() != $cost_issue_file->user_id) {
            flash('You are not authorized to do this action');
            return redirect('project/' . $cost_issue_file->project_id . '/issue-files');
        }

        $this->validate($request, ['subject' => 'required', 'period_id' => 'required', 'file' => 'file|mimes:xls,xlsx|max:2048']);

        $cost_issue_file->fill($request->only(['subject', 'period_id', 'note']));
        $cost_issue_file->file = $this->uploadFile($cost_issue_file, $request);
        $cost_issue_file->save();
        flash('File has been updated', 'success');
        return redirect('project/' . $cost_issue_file->project_id . '/issue-files');
    }

    public function destroy(CostIssueFile $cost_issue_file)
    {
        if (can('cost_owner', $cost_issue_file->project) || auth()->id() == $cost_issue_file->user_id) {
            $cost_issue_file->delete();
            flash('File has been deleted', 'info');
        } else {
            flash('You are not authorized to do this action');
        }

        return redirect('project/' . $cost_issue_file->project_id . '/issue-files');
    }

    protected function uploadFile(Project $project, CostIssueFile $cost_issue_file, Request $request)
    {
        $file = $request->file('file');

        if ((!$file || !$file->isValid()) && $cost_issue_file->exists) {
            return $cost_issue_file->file;
        }

        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $file->move($cost_issue_file->uploadDir(), $filename);

        return $filename;
    }
}
