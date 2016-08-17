<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
use Illuminate\Http\Request;

class ActivityDivisionController extends Controller
{

    protected $rules = ['name' => 'required', 'parent_id' => 'sometimes|exists:activity_divisions,id', 'code' => 'required'];

    public function index()
    {
        $activityDivisions = ActivityDivision::tree()->paginate();

        return view('activity-division.index', compact('activityDivisions'));
    }

    public function create()
    {
        return view('activity-division.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        ActivityDivision::create($request->all());

        flash('Activity division has been saved', 'success');

        return \Redirect::route('activity-division.index');
    }

    public function show(ActivityDivision $activity_division)
    {
        return view('activity-division.show', compact('activity_division'));
    }

    public function edit(ActivityDivision $activity_division)
    {
        return view('activity-division.edit', compact('activity_division'));
    }

    public function update(ActivityDivision $activity_division, Request $request)
    {
        $this->validate($request, $this->rules);

        $activity_division->update($request->all());

        flash('Activity division has been saved', 'success');

        return \Redirect::route('activity-division.index');
    }

    public function destroy(ActivityDivision $activity_division)
    {
        $activity_division->delete();

        flash('Activity division has been deleted', 'success');

        return \Redirect::route('activity-division.index');
    }
}
