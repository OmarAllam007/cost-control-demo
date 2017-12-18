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

    public function edit(GlobalPeriod $globalPeriod)
    {
        return view('global-periods.create');
    }

    public function update(Request $request, GlobalPeriod $globalPeriod)
    {
        $this->validate($request, config('validation.global_period', []));

        $globalPeriod->update($request->all());

        flash('Period has been updated', 'success');

        return \Redirect::route('global-periods.index');
    }

    public function destroy(GlobalPeriod $globalPeriod)
    {

        if (!$globalPeriod->hasProjectPeriods()) {
            $globalPeriod->delete();
            flash("Period has been deleted", 'info');
        } else {
            flash("Period is already used in projects.", 'warning');
        }

        return \Redirect::route('global-reports.index');
    }
}
