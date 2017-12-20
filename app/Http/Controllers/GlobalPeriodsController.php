<?php

namespace App\Http\Controllers;

use App\GlobalPeriod;
use App\Period;
use Illuminate\Http\Request;

class GlobalPeriodsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $globalPeriods = GlobalPeriod::paginate();

        return view('global-periods.index', compact('globalPeriods'));
    }


    public function create()
    {
        return view('global-periods.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, config('validation.global_period', []));

        GlobalPeriod::create($request->all());

        flash('Period has been created', 'success');

        return \Redirect::route('global-periods.index');
    }

    public function show($id)
    {
        //
    }

    public function edit(GlobalPeriod $global_period)
    {
        return view('global-periods.edit', compact('global_period'));
    }

    public function update(Request $request, GlobalPeriod $global_period)
    {
        $this->validate($request, config('validation.global_period', []));

        $global_period->update($request->all());

        flash('Period has been updated', 'success');

        return \Redirect::route('global-periods.index');
    }

    public function destroy(GlobalPeriod $global_period)
    {

        if (!$global_period->hasProjectPeriods()) {
            $global_period->delete();
            flash("Period has been deleted", 'info');
        } else {
            flash("Period is already used in projects.", 'warning');
        }

        return \Redirect::route('global-periods.index');
    }
}
