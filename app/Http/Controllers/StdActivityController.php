<?php

namespace App\Http\Controllers;

use App\Filter\StdActivityFilter;
use App\Http\Requests\WipeRequest;
use App\Jobs\ActivityImportJob;
use App\Jobs\Export\ExportStdActivitiesJob;
use App\Jobs\Export\Reports\Budget\ExportStdActivity;
use App\Jobs\Modify\ModifyPublicStdActivitiesJob;
use App\Project;
use App\StdActivity;
use Illuminate\Http\Request;

class StdActivityController extends Controller
{

    protected $rules = ['name' => 'required', 'division_id' => 'required|exists:activity_divisions,id', 'code' => 'required'];

    public function index()
    {
        if (\Gate::denies('read', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $filter = new StdActivityFilter(StdActivity::query(), session('filters.std-activity'));
        $stdActivities = $filter->filter()->paginate(100);

        return view('std-activity.index', compact('stdActivities'));
    }

    public function create()
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('std-activity.create');
    }

    public function store(Request $request)
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        $activity = StdActivity::create($request->all());
        $activity->syncVariables($request->get('variables'));

        flash('Std activity has been saved', 'success');

        return \Redirect::route('std-activity.index');
    }

    public function show(StdActivity $std_activity)
    {
        if (\Gate::denies('read', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('std-activity.show', compact('std_activity'));
    }

    public function edit(StdActivity $std_activity)
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('std-activity.edit', compact('std_activity'));
    }

    public function update(StdActivity $std_activity, Request $request)
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        $std_activity->update($request->all());
        $std_activity->syncVariables($request->get('variables'));

        flash('Std activity has been saved', 'success');

        return \Redirect::route('std-activity.index');
    }

    public function destroy(StdActivity $std_activity)
    {
        if (\Gate::denies('delete', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $std_activity->delete();

        flash('Std activity has been deleted', 'success');

        return \Redirect::route('std-activity.index');
    }

    function import()
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('std-activity.import');
    }

    function postImport(Request $request)
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, [
            'file' => 'required|file'//|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $status = $this->dispatch(new ActivityImportJob($file->path()));
        if ($status['dublicated']) {
            $dublicatedKey = 'std-dublicated';
            if(\Cache::has('std-dublicated')){
                \Cache::forget('std-dublicated');
            }
            \Cache::add($dublicatedKey, $status['dublicated'], 100);
            return redirect()->to(route('std-activity.dublicated'));
        }
        flash($status['success'] . ' Activities have been imported', 'success');
        return redirect()->route('std-activity.index');
    }

    function filters(Request $request)
    {
        if (\Gate::denies('read', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

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

    function exportAllActivities()
    {
        if (\Gate::denies('read', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->dispatch(new ExportStdActivitiesJob());
    }

    function modifyAllActivities()
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('std-activity.modify');
    }

    function postModifyAllActivities(Request $request)
    {
        if (\Gate::denies('write', 'std-activities')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $file = $request->file('file');
        $this->dispatch(new ModifyPublicStdActivitiesJob($file));

        return redirect()->back();

    }

    function dublicateActivity()
    {
        return view('std-activity.dublicated');
    }

    function exportStdActivityBudgetReport(Project $project){
        $this->dispatch(new ExportStdActivity($project));
    }
}
