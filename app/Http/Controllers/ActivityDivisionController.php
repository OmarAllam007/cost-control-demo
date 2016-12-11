<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class ActivityDivisionController extends Controller
{

    protected $rules = ['name' => 'required', 'parent_id' => 'sometimes|exists:activity_divisions,id', 'code' => 'required'];

    public function index()
    {
        if (\Gate::denies('read', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }
        $activityDivisions = ActivityDivision::tree()->appendActivity()->paginate(25);

        return view('activity-division.index', compact('activityDivisions'));
    }

    public function create()
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('activity-division.create');
    }

    public function store(Request $request)
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        ActivityDivision::create($request->all());

        flash('Activity division has been saved', 'success');

        return \Redirect::route('activity-division.index');
    }

    public function show(ActivityDivision $activity_division)
    {
        if (\Gate::denies('read', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('activity-division.show', compact('activity_division'));
    }

    public function edit(ActivityDivision $activity_division)
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('activity-division.edit', compact('activity_division'));
    }

    public function update(ActivityDivision $activity_division, Request $request)
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        $activity_division->update($request->all());

        flash('Activity division has been saved', 'success');

        return \Redirect::route('activity-division.index');
    }

    public function destroy(ActivityDivision $activity_division)
    {
        if (\Gate::denies('delete', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $activity_division->delete();

        flash('Activity division has been deleted', 'success');

        return \Redirect::route('activity-division.index');
    }

    public function import()
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $path = storage_path('files/division.csv');
        $handle = fopen($path, "r");

        if ($handle !== FALSE) {
            fgetcsv($handle);
            $divisions = ActivityDivision::query()->pluck('id', 'name')->toArray();

            while (($row = fgetcsv($handle)) !== FALSE) {
                $levels = array_filter($row);
                $parent_id = 0;
                foreach ($levels as $level) {
                    if (!isset($divisions[ $level ])) {
                        $division = ActivityDivision::create([
                            'name' => $level,
                            'parent_id' => $parent_id,
                        ]);

                        $divisions[ $level ] = $parent_id = $division->id;
                    } else {
                        $parent_id = $divisions[ $level ];
                    }
                }
            }


        }
        fclose($handle);
        return \Redirect::route('activity-division.index');
    }
}
