<?php

namespace App\Http\Controllers;

use App\Filter\UnitFilter;
use App\Http\Requests\WipeRequest;
use App\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{

    protected $rules = ['type' => 'required'];

    public function index()
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $filter = new UnitFilter(Unit::query(),session('filters.unit'));
        $units = $filter->filter()->paginate(10);
        return view('unit.index', compact('units'));
    }

    public function create()
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('unit.create');
    }

    public function store(Request $request)
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        Unit::create(['type'=>$request->type]);

        flash('Unit has been saved', 'success');

        return \Redirect::route('unit.index');
    }

    public function show(Unit $unit)
    {
        return view('unit.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('unit.edit', compact('unit'));
    }

    public function update(Unit $unit, Request $request)
    {
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        $unit->update($request->all());

        flash('Unit has been saved', 'success');

        return \Redirect::route('unit.index');
    }

    public function destroy(Unit $unit)
    {
        if (\Gate::denies('delete', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $unit->delete();

        flash('Unit has been deleted', 'success');

        return \Redirect::route('unit.index');
    }

    public function filter(Request $request)
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $data = $request->only('type');
        \Session::set('filters.unit',$data);
        return \Redirect::back();
    }

    function wipe(WipeRequest $request)
    {
        \DB::table('units')->delete();
        flash('All units have been deleted', 'info');
        return \Redirect::route('unit.index');
    }
}
