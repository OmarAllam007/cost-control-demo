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
        return view('actual-revenue.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $file = $request->file('file');
        $this->dispatch(new ImportActualRevenue($file->path(), $project));
        return view('actual-revenue.import',compact('project'));

    }
}