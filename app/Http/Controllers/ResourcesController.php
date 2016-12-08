<?php

namespace App\Http\Controllers;

use App\BusinessPartner;
use App\Filter\ResourcesFilter;
use App\Http\Requests\WipeRequest;
use App\Jobs\CacheResourcesTree;
use App\Jobs\Export\ExportPublicResourcesJob;
use App\Jobs\ImportResourceCodesJob;
use App\Jobs\Export\ExportResourcesJob;
use App\Jobs\Modify\ModifyPublicResourcesJob;
use App\Jobs\Modify\ModifyResourcesJob;
use App\Jobs\ResourcesImportJob;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\StdActivityResource;
use App\Unit;
use App\UnitAlias;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use phpDocumentor\Reflection\Types\Null_;

class ResourcesController extends Controller
{

    protected $rules = ['name' => 'required', 'resource_code' => 'unique:resources'];

    public function index()
    {
        $filter = new ResourcesFilter(Resources::query(), session('filters.resources'));
        $resources = $filter->filter()->basic()->orderBy('resource_code')->orderBy('name')->paginate(100);
        return view('resources.index', compact('resources'));
    }

    public function create()
    {
        $units_drop = Unit::options();
        $partners = BusinessPartner::options();
        $resource_types = ResourceType::lists('name', 'id')->all();
        $edit = false;

        return view('resources.create', compact('partners', 'resource_types', 'units_drop', 'edit'));
    }

    public function store(Request $request)
    {
        $request['project_id'] = $request->project;

        $this->validate($request, $this->rules);
        if ($request['waste'] <= 1) {
            $request['waste'] = $request->waste;
        } else {
            $request['waste'] = ($request->waste / 100);
        }
        $resource = Resources::create($request->all());

        flash('Resource has been saved', 'success');

        if ($resource->project_id) {
            return \Redirect::route('project.show', $resource->project_id);
        }
        return \Redirect::route('resources.index');
    }

    public function show(Resources $resource)
    {
        return view('resources.show', compact('resource'));
    }

    public function edit(Resources $resources)
    {
        $partners = BusinessPartner::options();
        $resource_types = ResourceType::lists('name', 'id')->all();
        $units_drop = Unit::options();
        $edit = true;

        return view('resources.edit', compact('resources', 'partners', 'resource_types', 'units_drop', 'edit'));
    }

    public function update(Resources $resources, Request $request)
    {
//        $this->validate($request, $this->rules);
        if ($request['waste'] <= 1) {
            $request['waste'] = $request->waste;
        } else {
            $request['waste'] = ($request->waste / 100);
        }


        $resources->update($request->all());

        $resources->syncCodes($request->get('codes'));

        flash('Resource has been saved', 'success');
        if ($resources->project_id) {
            return \Redirect::route('project.show', $resources->project_id);
        }
        return \Redirect::route('resources.index');
    }

    public function destroy(Resources $resources)
    {
        $resources->delete();

        flash('Resources has been deleted', 'success');

        return \Redirect::route('resources.index');
    }

    function import()
    {
        return view('resources.import');
    }

    function postImport(Request $request)
    {
        $project = $request->project;
        $this->validate($request, [
            'file' => 'required|file'//|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $status = $this->dispatch(new ResourcesImportJob($file->path(), $project));

        if ($status['failed']->count()) {
            $key = 'res_' . time();
            \Cache::add($key, $status, 180);

            flash('Could not import some resources', 'warning');
            return redirect()->route('resources.fix-import', $key);
        }
        if (count($status['dublicated'])) {
            flash($status['success'] . ' items have been imported', 'success');
            return \Redirect::route('resources.index', ['dublicate' => $status['dublicated']]);
        }

        flash($status['success'] . ' Resources have been imported', 'success');
        return redirect()->route('resources.index');
    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('resources.index');
        }

        $status = \Cache::get($key);
        $items = $status['failed'];

        return view('resources.fix-import', compact('items', 'key'));
    }

    function postFixImport(Request $request, $key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('resources.index');
        }

        $data = $request->get('data');
        $errors = Resources::checkFixImport($data);
        $status = \Cache::get($key);

        if (!$errors) {
            $units = $data['units'];

            Resources::flushEventListeners();
            foreach ($status['failed'] as $item) {
                if (isset($units[$item['orig_unit']])) {
                    $item['unit'] = $units[$item['orig_unit']];
                    Resources::create($item);
                    $status['success']++;

                    UnitAlias::createAliasFor($item['unit'], $item['orig_unit']);
                }
            }

            $this->dispatch(new CacheResourcesTree());

            flash($status['success'] . ' Resources have been imported', 'success');
            return \Redirect::route('resources.index');
        }

        flash('Could not update resources', 'warning');
        return \Redirect::back()->withErrors($errors)->withInput($data);
    }

    function override(Resources $resources, Project $project)
    {
        $override = true;
        $overwrote = Resources::version($project->id, $resources->id)->first();

        if (!$overwrote) {
            $overwrote = $resources;
        }

        return view('resources.override',
            ['resource' => $overwrote, 'baseResource' => $resources, 'project' => $project, 'override' => $override]);
    }

    function postOverride(Resources $resources, Project $project, Request $request)
    {
        $this->validate($request, $this->rules);
        $newResource = Resources::version($project->id, $resources->id)->first();

        if (!$newResource) {
            $newResource = new Resources($request->all());
            $newResource->resource_code = $resources->resource_code;
            $newResource->project_id = $project->id;
            $newResource->resource_id = $resources->id;
            Resources::flushEventListeners();
            $newResource->save();
            $newResource->updateBreakdownResurces();
        } else {

            $newResource->update($request->all());
            $newResource->updateBreakdownResurces();
        }
        flash('Resource has been updated successfully', 'success');
        return redirect()->route('project.show', $project);
    }

    function filter(Request $request)
    {
        $data = $request->only(['name', 'unit', 'resource_type_id', 'resource_code']);
        \Session::set('filters.resources', $data);

        return \Redirect::route('resources.index');
    }

    public function exportResources(Project $project)
    {
        $this->dispatch(new ExportResourcesJob($project));
    }

    function wipe(WipeRequest $request)
    {
        \DB::table('resources')->truncate();
        \DB::table('std_activity_resources')->truncate();
        \DB::table('resource_types')->truncate();

        flash('All resources have been deleted', 'info');

        return \Redirect::route('resources.index');
    }

    function importCodes()
    {
        return view('resources.import-codes');
    }

    function postImportCodes(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $count = $this->dispatch(new ImportResourceCodesJob($file->path()));

        flash($count . ' Equivalent codes have been imported successfully', 'success');
        return \Redirect::route('resources.index');
    }


    public function exportAllResources()
    {
        return $this->dispatch(new ExportPublicResourcesJob());

    }

    public function modifyAllResources()
    {

        return view('resources.modify', ['project_id' => request('project')]);
    }

    public function postModifyAllResources(Request $request)
    {
        $project = $request->project;
        $file = $request->file('file');
        $this->dispatch(new ModifyPublicResourcesJob($file, $project));

        $filter = new ResourcesFilter(Resources::query(), session('filters.resources'));
        $resources = $filter->filter()->basic()->orderBy('resource_code')->orderBy('name')->paginate(100);
        return view('resources.index', ['resources' => $resources]);
    }
}
