<?php

namespace App\Http\Controllers;


use App\Breakdown;
use App\Http\Requests\BreakdownRequest;
use Illuminate\Http\Request;

class BreakdownController extends Controller
{

    public function create()
    {
        return view('breakdown.create');
    }

    public function store(BreakdownRequest $request)
    {
        $breakdown = Breakdown::create($request->all());
        $breakdown->resources()->createMany($request->get('resources'));

        return \Redirect::route('project.show', $breakdown->project_id);
    }

    public function edit(Breakdown $breakdown)
    {
        return view('breakdown.edit', compact('breakdown'));
    }

    public function update(Request $request, Breakdown $breakdown)
    {

    }

    public function delete(Breakdown $breakdown)
    {

    }
}