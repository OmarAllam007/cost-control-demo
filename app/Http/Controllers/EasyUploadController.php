<?php

namespace App\Http\Controllers;

use App\Jobs\EasyUploadJob;
use App\Project;
use Illuminate\Http\Request;

class EasyUploadController extends Controller
{
    function create(Request $request, Project $project)
    {
        if (!can('breakdown', $project)) {
            flash('This action is not authorized');
            if ($request->has('iframe')) {
                return redirect('/blank');
            } else {
                return redirect('/');
            }
        }

        return view('easy-upload.create', compact('project'));
    }

    function store(Request $request, Project $project)
    {
        if (!can('breakdown', $project)) {
            flash('This action is not authorized');
            if ($request->has('iframe')) {
                return back();
            } else {
                return redirect('/');
            }
        }

        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $status = $this->dispatchNow(new EasyUploadJob($project, $file->path()));

        if ($status['failed']) {
            return view('easy-upload.failed', compact('project', 'status'));
        }

        flash('File has been imported successfully', 'success');
        return redirect('/blank?reload=breakdowns');
    }
}
