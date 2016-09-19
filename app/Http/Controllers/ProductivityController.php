<?php
namespace App\Http\Controllers;

use App\ActivityDivision;
use App\CsiCategory;
use App\Jobs\ProductivityImportJob;
use App\Productivity;
use App\Project;
use App\Unit;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{

    protected $rules = ['' => ''];

    public function index()
    {

        $productivities = Productivity::paginate();
        $categories = CsiCategory::tree()->paginate();

        return view('productivity.index', compact('productivities','categories'));
    }

    public function create()
    {
        $csi_category = CsiCategory::lists('name', 'id')->all();
        $units_drop = Unit::options();
        $edit = false;


        return view('productivity.create', compact('csi_category', 'units_drop','edit'));
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
        $csi_category = CsiCategory::lists('name', 'id')->all();
        $units_drop = Unit::lists('type', 'id')->all();
        $edit = true;
        return view('productivity.edit', compact('productivity', 'units_drop', 'csi_category','edit'));
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
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $this->dispatch(new ProductivityImportJob($file->path()));

        return redirect()->route('productivity.index');
    }

    function override(Productivity $productivity, Project $project)
    {
        $overwrote = Productivity::version($project->id, $productivity->id)->first();
        $edit = true ;
        if (!$overwrote) {
            $overwrote = $productivity;
        }

        return view('productivity.override', ['productivity' => $overwrote, 'baseProductivity' => $productivity, 'project' => $project,'edit'=>$edit]);
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
}
