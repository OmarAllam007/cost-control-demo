<?php

namespace App\Http\Controllers;

use App\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $units = Unit::paginate();

        return view('unit.index', compact('units'));
    }

    public function create()
    {
        return view('unit.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        Unit::create($request->all());

        flash('Unit has been saved', 'success');

        return \Redirect::route('unit.index');
    }

    public function show(Unit $unit)
    {
        return view('unit.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {

        return view('unit.edit', compact('unit'));
    }

    public function update(Unit $unit, Request $request)
    {
        $this->validate($request, $this->rules);

        $unit->update($request->all());

        flash('Unit has been saved', 'success');

        return \Redirect::route('unit.index');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        flash('Unit has been deleted', 'success');

        return \Redirect::route('unit.index');
    }
}
