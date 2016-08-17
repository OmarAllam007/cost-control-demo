<?php

namespace App\Http\Controllers;

use App\StdActivity;
use Illuminate\Http\Request;

class StdActivityController extends Controller
{

    protected $rules = ['name' => 'required', 'division_id' => 'required|exists:activity_divisions,id', 'code' => 'required'];

    public function index()
    {
        $stdActivities = StdActivity::paginate();

        return view('std-activity.index', compact('stdActivities'));
    }

    public function create()
    {
        return view('std-activity.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        StdActivity::create($request->all());

        flash('Std activity has been saved', 'success');

        return \Redirect::route('std-activity.index');
    }

    public function show(StdActivity $std_activity)
    {
        return view('std-activity.show', compact('std_activity'));
    }

    public function edit(StdActivity $std_activity)
    {
        return view('std-activity.edit', compact('std_activity'));
    }

    public function update(StdActivity $std_activity, Request $request)
    {
        $this->validate($request, $this->rules);

        $std_activity->update($request->all());

        flash('Std activity has been saved', 'success');

        return \Redirect::route('std-activity.index');
    }

    public function destroy(StdActivity $std_activity)
    {
        $std_activity->delete();

        flash('Std activity has been deleted', 'success');

        return \Redirect::route('std-activity.index');
    }
}
