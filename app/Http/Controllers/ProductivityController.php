<?php
namespace App\Http\Controllers;

use App\ActivityDivision;
use App\CsiCategory;
use App\Filter\ProductivityFilter;
use App\Http\Controllers\Reports\Export\ExportProductivityReport;
use App\Http\Requests\WipeRequest;
use App\Jobs\CacheCsiCategoryTree;
use App\Jobs\Export\ExportProductivityJob;
use App\Jobs\Export\ExportPublicProductivitiesJob;
use App\Jobs\Modify\ModifyPublicProductivitiesJob;
use App\Jobs\ProductivityImportJob;
use App\LaborTrendUploadTable;
use App\Productivity;
use App\ProductivityList;
use App\Project;
use App\TrendUploadTable;
use App\Unit;
use App\UnitAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductivityController extends Controller
{

    protected $rules = ['' => ''];

    public function index()
    {
        if (\Gate::denies('read', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $filter = new ProductivityFilter(Productivity::query(), session('filters.productivity'));
        $productivities = $filter->filter()->basic()->paginate(100);
        return view('productivity.index', compact('productivities'));
    }

    public function create()
    {
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $edit = false;
        return view('productivity.create')->with('edit', $edit);
    }

    public function store(Request $request)
    {
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        $this->after_reduction = ($request->reduction_factor * $request->daily_output) + $request->daily_output;

        Productivity::create($request->all());

        flash('Productivity has been saved', 'success');

        $this->dispatch(new CacheCsiCategoryTree());
        return \Redirect::route('productivity.index');
    }

    public function show(Productivity $productivity)
    {
        if (\Gate::denies('read', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $project = Project::where('id', $productivity->project_id)->first();
        return view('productivity.show', compact('productivity', 'project'));
    }

    public function edit(Productivity $productivity)
    {
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $csi_category = CsiCategory::lists('name', 'id')->all();
        $units_drop = Unit::lists('type', 'id')->all();
        $edit = true;
        return view('productivity.edit', compact('productivity', 'units_drop', 'csi_category', 'edit'));
    }


    public function update(Productivity $productivity, Request $request)
    {
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);
        $productivity->after_reduction = ($request->reduction_factor * $request->daily_output) + $request->daily_output;

        $productivity->update($request->all());
        flash('Productivity has been saved', 'success');

        return \Redirect::route('productivity.index');
    }

    public function destroy(Productivity $productivity)
    {
        if (\Gate::denies('delete', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $productivity->delete();

        flash('Productivity has been deleted', 'success');

        return \Redirect::route('productivity.index');
    }

    function import()
    {
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('productivity.import');
    }

    function postImport(Request $request)
    {
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, [
            'file' => 'required|file'//|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $status = $this->dispatch(new ProductivityImportJob($file->path()));
        if ($status['failed']->count()) {
            $key = 'prod_' . time();
            \Cache::add($key, $status, 180);
            flash('Could not import all items', 'warning');
            return \Redirect::route('productivity.fix-import', $key);
        }
        if (count($status['dublicated'])) {
            flash($status['success'] . ' items have been imported', 'success');
            return \Redirect::route('productivity.index', ['dublicate' => $status['dublicated']]);
        }

        flash($status['success'] . ' items have been imported', 'success');
        return redirect()->route('productivity.index');
    }

    function override(Productivity $productivity, Project $project)
    {
        if ($productivity->project_id) {
            $overwrote = $productivity;
            $baseProductivity = Productivity::find($productivity->productivity_id);
        } else {
            $overwrote = Productivity::version($project->id, $productivity->id)->first();
            if (!$overwrote) {
                $overwrote = $productivity;
            }
            $baseProductivity = $productivity;
        }

        return view('productivity.override', [
            'productivity' => $overwrote,
            'baseProductivity' => $baseProductivity,
            'project' => $project,
            'edit' => false,
            'override' => true
        ]);
    }

    function postOverride(Request $request, Productivity $productivity, Project $project)
    {
        $this->validate($request, $this->rules);
        if ($productivity->project_id) {
            $newProductivity = $productivity;
            $baseProductivity = Productivity::find($productivity->productivity_id);
        } else {
            $newProductivity = Productivity::version($project->id, $productivity->id)->first();
            $baseProductivity = $productivity;
        }

        if (!$newProductivity) {
            $newProductivity = new Productivity($request->all());
            $newProductivity->project_id = $project->id;
            $newProductivity->productivity_id = $baseProductivity->id;
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
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

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
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

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
        if (\Gate::denies('read', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->dispatch(new ExportProductivityJob($project));
    }

    function wipe(WipeRequest $request)
    {
        \DB::table('productivities')->delete();
        \DB::table('csi_categories')->delete();


        flash('All productivities have been deleted', 'info');

        return \Redirect::route('productivity.index');
    }

    function exportPublicProductivities()
    {
        if (\Gate::denies('read', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->dispatch(new ExportPublicProductivitiesJob());
    }

    function modifyAllProductivities()
    {
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('productivity.modify');
    }

    function postModifyAllProductivities(Request $request)
    {
        if (\Gate::denies('write', 'productivity')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $file = $request->file('file');
        $this->dispatch(new ModifyPublicProductivitiesJob($file));
        return redirect()->action('ProductivityController@index');
    }

    function importReport($project)
    {
        $project = Project::find($project);
        $data = TrendUploadTable::where('project_id', $project->id)->get();
        return view('reports.cost-control.productivity.import_productivity')->with(['project' => $project, 'data' => $data]);
    }

    function postImportReport(Project $project, Request $request)
    {
        $file = $request->file('file');
        $version = 1;
        $lastOne = TrendUploadTable::orderBy('created_at', 'desc')->where('project_id', $project->id)->where('period_id', $project->getMaxPeriod())->first();
        if ($lastOne) {
            $last_ver = substr($lastOne->file_path, strpos($lastOne->file_path, '.xlsx') - 1, 1);
            $version = $last_ver+1;
            $file->move(storage_path('app/productivity_trend/'), 'productivitytrend' . $project->id . 'period_id' . $project->getMaxPeriod() . 'ver' . $version . '.xlsx');
            $path = storage_path('app/productivity_trend/') . 'productivitytrend' . $project->id . 'period_id' . $project->getMaxPeriod() . 'ver' . $version . '.xlsx';
            TrendUploadTable::create(['uploaded_by' => \Auth::user()->id, 'file_path' => $path, 'period_id' => $project->getMaxPeriod(), 'project_id' => $project->id]);
        } else {
            $file->move(storage_path('app/productivity_trend/'), 'productivitytrend' . $project->id . 'period_id' . $project->getMaxPeriod() . 'ver' . $version . '.xlsx');
            $path = storage_path('app/productivity_trend/') . 'productivitytrend' . $project->id . 'period_id' . $project->getMaxPeriod() . 'ver' . $version . '.xlsx';
            TrendUploadTable::create(['uploaded_by' => \Auth::user()->id, 'file_path' => $path, 'period_id' => $project->getMaxPeriod(), 'project_id' => $project->id]);
        }
        return redirect()->back();

    }

    function downloadTrend($id)
    {
        $file_path = TrendUploadTable::find($id)->file_path;
        return response()->download($file_path);
    }

    function laborImportReport($project)
    {
        $project = Project::find($project);
        $data = LaborTrendUploadTable::where('project_id', $project->id)->get();
        return view('reports.cost-control.labor_trend.labor_trend')->with(['project' => $project, 'data' => $data]);
    }

    function laborPostImportReport(Project $project, Request $request)
    {
        $file = $request->file('file');
        $version = 1;
        $lastOne = LaborTrendUploadTable::orderBy('created_at', 'desc')->where('project_id', $project->id)->where('period_id', $project->getMaxPeriod())->first();
        if ($lastOne) {
            $last_ver = substr($lastOne->file_path, strpos($lastOne->file_path, '.xlsx') - 1, 1);
            $version = $last_ver+1;
            $file->move(storage_path('app/labor_trend/'), 'labor_trend' . $project->id . 'period_id' . $project->getMaxPeriod() . 'ver' . $version . '.xlsx');
            $path = storage_path('app/labor_trend/') . 'labor_trend' . $project->id . 'period_id' . $project->getMaxPeriod() . 'ver' . $version . '.xlsx';
            LaborTrendUploadTable::create(['uploaded_by' => \Auth::user()->id, 'file_path' => $path, 'period_id' => $project->getMaxPeriod(), 'project_id' => $project->id]);
        } else {
            $file->move(storage_path('app/labor_trend/'), 'labor_trend' . $project->id . 'period_id' . $project->getMaxPeriod() . 'ver' . $version . '.xlsx');
            $path = storage_path('app/labor_trend/') . 'labor_trend' . $project->id . 'period_id' . $project->getMaxPeriod() . 'ver' . $version . '.xlsx';
            LaborTrendUploadTable::create(['uploaded_by' => \Auth::user()->id, 'file_path' => $path, 'period_id' => $project->getMaxPeriod(), 'project_id' => $project->id]);

        }
        return redirect()->back();
    }

    function downloadLaborTrend($id)
    {
        $file_path = LaborTrendUploadTable::find($id)->file_path;
        return response()->download($file_path);
    }

    function exportProductivityReport(Project $project){
        $this->dispatch(new ExportProductivityJob($project));
    }


}
