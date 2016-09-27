<?php
namespace App\Http\Controllers;

use App\ActivityDivision;
use App\CsiCategory;
use App\Filter\ProductivityFilter;
use App\Jobs\ProductivityImportJob;
use App\Productivity;
use App\ProductivityList;
use App\Project;
use App\Unit;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{

    protected $rules = ['' => ''];

    public function index()
    {

        $filter = new ProductivityFilter(Productivity::query(), session('filters.productivity'));
        $productivities = $filter->filter()->paginate(100);
        return view('productivity.index', compact('productivities'));
    }

    public function create()
    {
        $edit = false;

        return view('productivity.create')->with('edit', $edit);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $this->after_reduction = ($request->reduction_factor * $request->daily_output) + $request->daily_output;

//        $man_hours  = $this->manHour($request);
//        $equip_hours = $this->equipHour($request);
         Productivity::create($request->all());
//        $productivity->update(['man_hours' => array_sum($man_hours), 'equip_hours' => array_sum($equip_hours)]);

        flash('Productivity has been saved', 'success');

        return \Redirect::route('productivity.index');
    }

    public function show(Productivity $productivity)
    {
        return view('productivity.show', compact('productivity'));
    }

    public function edit(Productivity $productivity)
    {
        $csi_category = CsiCategory::lists('name', 'id')->all();
        $units_drop = Unit::lists('type', 'id')->all();
        $edit = true;
        return view('productivity.edit', compact('productivity', 'units_drop', 'csi_category', 'edit'));
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

    function import()
    {
        return view('productivity.import');
    }

    function postImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $this->dispatch(new ProductivityImportJob($file->path()));

        return redirect()->route('productivity.index');
    }

    function override(Productivity $productivity, Project $project)
    {
        $overide = true;
        $overwrote = Productivity::version($project->id, $productivity->id)->first();
        $edit = true;
        if (!$overwrote) {
            $overwrote = $productivity;
        }

        return view('productivity.override', ['productivity' => $overwrote, 'baseProductivity' => $productivity, 'project' => $project, 'edit' => $edit]);
    }

    function postOverride(Request $request, Productivity $productivity, Project $project)
    {
        $this->validate($request, $this->rules);

        $newProductivity = Productivity::version($project->id, $productivity->id)->first();

        if (!$newProductivity) {
            $newProductivity = new Productivity($request->all());
            $newProductivity->project_id = $project->id;
            $newProductivity->productivity_id = $productivity->id;
            $newProductivity->save();
        } else {
            $newProductivity->update($request->all());
        }

        flash('Productivity has been updated successfully', 'success');
        return redirect()->route('project.show', $project);
    }

    public function filter(Request $request)
    {
        $data = $request->only(['csi_category_id', 'code', 'description', 'source']);
        \Session::set('filters.productivity', $data);
        return \Redirect::back();
    }
}
