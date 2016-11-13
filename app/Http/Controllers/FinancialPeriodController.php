<?php

namespace App\Http\Controllers;

use App\FinancialPeriod;
use App\Project;
use Illuminate\Http\Request;
use App\Http\Requests;

class FinancialPeriodController extends Controller
{

    public function index(Project $project)
    {
        return view('financial-period.index')->with(['project' => $project]);
    }

    public function create(Project $project)
    {
        return view('financial-period.create')->with(['project' => $project]);
    }

    public function store(Project $project, Request $request)
    {
        $request['project_id'] = $project->id;
        FinancialPeriod::create($request->all());
        return view('financial-period.index')->with(['project' => $project]);
    }


    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}
