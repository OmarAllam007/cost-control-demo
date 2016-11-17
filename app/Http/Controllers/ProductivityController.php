<?php
namespace App\Http\Controllers;

use App\ActivityDivision;
use App\CsiCategory;
use App\Filter\ProductivityFilter;
use App\Http\Requests\WipeRequest;
use App\Jobs\Export\ExportProductivityJob;
use App\Jobs\ProductivityImportJob;
use App\Productivity;
use App\ProductivityList;
use App\Project;
use App\Unit;
use App\UnitAlias;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{

    protected $rules = ['' => ''];

    public function index()
    {
        $filter = new ProductivityFilter(Productivity::query(), session('filters.productivity'));
        $productivities = $filter->filter()->basic()->paginate(100);
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


        Productivity::create($request->all());


        flash('Productivity has been saved', 'success');

        return \Redirect::route('productivity.index');
    }

    public function show(Productivity $productivity)
    {
        $project = Project::where('id', $productivity->project_id)->first();
        return view('productivity.show', compact('productivity', 'project'));
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

        $status = $this->dispatch(new ProductivityImportJob($file->path()));
        if ($status['failed']->count()) {
            $key = 'prod_' . time();
            \Cache::add($key, $status, 180);
            flash('Could not import all items', 'warning');
            return \Redirect::route('productivity.fix-import', $key);
        }

        flash($status['success'] . ' items have been imported', 'success');
        return redirect()->route('productivity.index');
    }

    function override(Productivity $productivity, Project $project)
    {
        $overide = true;
        $overwrote = Productivity::version($project->id, $productivity->id)->first();
        if (!$overwrote) {
            $overwrote = $productivity;
        }

        return view('productivity.override', [
            'productivity' => $overwrote,
            'baseProductivity' => $productivity,
            'project' => $project,
            'edit' => false,
        ]);
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

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('productivity.index');
        }

        $status = \Cache::get($key);
        $items = $status['failed'];

        return view('productivity.fix-import', compact('items', 'key'));
    }

    function postFixImport(Request $request, $key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('productivity.index');
        }

        $data = $request->get('data');
        $errors = Productivity::checkFixImport($data);
        if (!$errors) {
            $status = \Cache::get($key);

            foreach ($status['failed'] as $item) {
                if (isset($data['units'][$item['orig_unit']])) {
                    $item['unit'] = $data['units'][$item['orig_unit']];
                    Productivity::create($item);
                    UnitAlias::createAliasFor($item['unit'], $item['orig_unit']);
                    ++$status['success'];
                }
            }

            flash($status['success'] . ' items have been imported', 'success');
            return \Redirect::route('productivity.index');
        }

        flash('Could not import all items');
        return \Redirect::route('productivity.fix-import', $key)
            ->withErrors($errors)->withInput($request->all());
    }

    public function showReport()
    {
        $projects = Project::paginate();
        return view('productivity.productivity_project', compact('projects'));
    }

    public function exportProductivity(Project $project)
    {
        $this->dispatch(new ExportProductivityJob($project));
    }

    function wipe(WipeRequest $request)
    {
        \DB::table('productivities')->delete();
        \DB::table('csi_categories')->delete();


        flash('All productivities have been deleted', 'info');

        return \Redirect::route('productivity.index');
    }
}
