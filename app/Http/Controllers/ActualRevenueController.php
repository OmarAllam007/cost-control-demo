<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 01/01/17
 * Time: 08:29 ص
 */

namespace App\Http\Controllers;


use App\Jobs\Import\ImportActualRevenue;
use Illuminate\Http\Request;
use App\Project;

class ActualRevenueController extends Controller
{

    function import(Project $project)
    {
        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        return view('actual-revenue.import', compact('project', 'periods'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, ['period_id' => 'required', 'file' => 'required|file|mimes:xls,xlsx|max:1024']);
        $file = $request->file('file');

        $count = $this->dispatch(new ImportActualRevenue($file->path(), $request->input('period_id')));

        flash("$count rows has been imported", 'info');
        return redirect()->back();
    }
}