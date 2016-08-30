<?php

namespace App\Http\Controllers;

use App\BoqDivision;
use Illuminate\Http\Request;

class BoqDivisionController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $boqDivisions = BoqDivision::tree()->paginate();
        return view('boq-division.index', compact('boqDivisions'));
    }

    public function create()
    {
        return view('boq-division.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        BoqDivision::create($request->all());

        flash('Boq division has been saved', 'success');

        return \Redirect::route('boq-division.index');
    }

    public function show(BoqDivision $boq_division)
    {
        return view('boq-division.show', compact('boq_division'));
    }

    public function edit(BoqDivision $boq_division)
    {
        return view('boq-division.edit', compact('boq_division'));
    }

    public function update(BoqDivision $boq_division, Request $request)
    {
        $this->validate($request, $this->rules);

        $boq_division->update($request->all());

        flash('Boq division has been saved', 'success');

        return \Redirect::route('boq-division.index');
    }

    public function destroy(BoqDivision $boq_division)
    {
        $boq_division->delete();

        flash('Boq division has been deleted', 'success');

        return \Redirect::route('boq-division.index');
    }
}
