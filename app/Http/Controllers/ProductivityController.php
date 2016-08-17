<?php

namespace App\Http\Controllers;

use App\CSI_category;
use App\Productivity;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $productivities = Productivity::paginate();

        return view('productivity.index', compact('productivities'));
    }

    public function create()
    {
        $csi_category = CSI_category::lists('name','id')->all();
        return view('productivity.create',compact('csi_category'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

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
        return view('productivity.edit', compact('productivity'));
    }

    public function update(Productivity $productivity, Request $request)
    {
        $this->validate($request, $this->rules);

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
