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
        $filter = new UnitFilter(Unit::query(),session('filters.unit'));
        $units = $filter->filter()->paginate(10);
        return view('unit.index', compact('units'));
    }

    public function create()
    {
        return view('unit.create');
    }

    public function store(Request $request)
    {
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

    public function filter(Request $request)
    {
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
