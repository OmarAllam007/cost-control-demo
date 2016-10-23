<?php

namespace App\Http\Controllers;

use App\Filter\StdActivityFilter;
use App\Jobs\ActivityImportJob;
use App\StdActivity;
use Illuminate\Http\Request;

class StdActivityController extends Controller
{

    protected $rules = ['name' => 'required', 'division_id' => 'required|exists:activity_divisions,id', 'code' => 'required'];

    public function index()
    {
        $filter  = new StdActivityFilter(StdActivity::query(), session('filters.std-activity'));
        $stdActivities = $filter->filter()->paginate(100);

        return view('std-activity.index', compact('stdActivities'));
    }

    public function create()
    {
        return view('std-activity.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        StdActivity::create($request->all());

        flash('Std activity has been saved', 'success');

        return \Redirect::route('std-activity.index');
    }

    public function show(StdActivity $std_activity)
    {
        return view('std-activity.show', compact('std_activity'));
    }

    public function edit(StdActivity $std_activity)
    {
        return view('std-activity.edit', compact('std_activity'));
    }

    public function update(StdActivity $std_activity, Request $request)
    {
        $this->validate($request, $this->rules);

        $std_activity->update($request->all());

        flash('Std activity has been saved', 'success');

        return \Redirect::route('std-activity.index');
    }

    public function destroy(StdActivity $std_activity)
    {
        $std_activity->delete();

        flash('Std activity has been deleted', 'success');

        return \Redirect::route('std-activity.index');
    }

    function import()
    {
        return view('std-activity.import');
    }

    function postImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $this->dispatch(new ActivityImportJob($file->path()));

        flash('Activities have been imported', 'success');
        return redirect()->route('std-activity.index');
    }

    function filters(Request $request)
    {
        $data = $request->only(['name', 'division_id']);
        \Session::set('filters.std-activity', $data);

        return \Redirect::back();
    }

    function wipe(WipeRequest $request)
    {
        \DB::table('std_activity_resources')->delete();
        \DB::table('breakdown_templates')->delete();
        \DB::table('std_activities')->delete();
        \DB::table('activity_divisions')->delete();

        flash('All activities have been deleted', 'info');

        return \Redirect::route('std-activity.index');
    }
}
