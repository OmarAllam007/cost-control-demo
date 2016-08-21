<?php

namespace App\Http\Controllers;

use App\CSI_category;
use App\Productivity;
use App\Unit;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{

    protected $rules = ['' => ''];

    public function index()
    {
        $productivities = Productivity::paginate();

        return view('productivity.index', compact('productivities'));
    }

    public function create()
    {
        $csi_category = CSI_category::lists('name','id')->all();
        $units_drop = Unit::lists('type', 'id')->all();


        return view('productivity.create',compact('csi_category','units_drop'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $this->after_reduction = ($request->reduction_factor * $request->daily_output) + $request->daily_output;
        Productivity::create($request->all());

        flash('Productivity has been saved', 'success');

        return \Redirect::route('productivity.index');
    }

    public function show(Productivity $productivity)
    {
        return view('productivity.show', compact('productivity'));
    }

    public function edit(Productivity $productivity)
    {
        $csi_category = CSI_category::lists('name','id')->all();
        $units_drop = Unit::lists('type', 'id')->all();

        return view('productivity.edit', compact('productivity','units_drop','csi_category'));
    }

    public function update(Productivity $productivity, Request $request)
    {
        $this->validate($request, $this->rules);
        $productivity->after_reduction = ($request->reduction_factor * $request->daily_output) + $request->daily_output;
        $productivity->update($request->all());

        flash('Productivity has been saved', 'success');

        return \Redirect::route('productivity.index');
    }

    public function destroy(Productivity $productivity)
    {
        $productivity->delete();

        flash('Productivity has been deleted', 'success');

        return \Redirect::route('productivity.index');
    }
}
