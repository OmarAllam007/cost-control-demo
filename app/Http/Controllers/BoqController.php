<?php

namespace App\Http\Controllers;

use App\Boq;
use App\WbsLevel;
use Illuminate\Http\Request;

class BoqController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $boqs = Boq::paginate();

        return view('boq.index', compact('boqs'));
    }

    public function create()

    {
        $wbsLevels = WbsLevel::tree()->paginate();

        return view('boq.create',compact('wbsLevels'));
    }

    public function store(Request $request)
    {


        Boq::create($request->all());

        flash('Boq has been saved', 'success');

        return \Redirect::route('boq.index');
    }

    public function show(Boq $boq)
    {
        return view('boq.show', compact('boq'));
    }

    public function edit(Boq $boq)
    {
        return view('boq.edit', compact('boq'));
    }

    public function update(Boq $boq, Request $request)
    {
        //$this->validate($request, $this->rules);
        $boq->update($request->all());
        flash('Boq has been saved', 'success');

        return \Redirect::route('boq.index');
    }

    public function destroy(Boq $boq)
    {
        $boq->delete();

        flash('Boq has been deleted', 'success');

        return \Redirect::route('boq.index');
    }
}
