<?php

namespace App\Http\Controllers;

use App\BusinessPartner;
use App\CostResource;
use App\Filter\ResourcesFilter;
use App\Http\Controllers\Caching\ResourcesCache;
use App\Http\Controllers\Reports\Productivity;
use App\Http\Requests\WipeRequest;
use App\Jobs\CacheResourcesTree;
use App\Jobs\Export\ExportCostResources;
use App\Jobs\Export\ExportPublicResourcesJob;
use App\Jobs\Export\ExportResourcesMapping;
use App\Jobs\ImportResourceCodesJob;
use App\Jobs\Export\ExportResourcesJob;
use App\Jobs\Modify\ModifyPublicResourcesJob;
use App\Jobs\Modify\ModifyResourcesJob;
use App\Jobs\ResourcesImportJob;
use App\Project;
use App\ResourceCode;
use App\Resources;
use App\ResourceType;
use App\StdActivityResource;
use App\Unit;
use App\UnitAlias;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class ResourcesController extends Controller
{
    protected $rules = ['name' => 'required', 'resource_code' => 'unique:resources'];

    public function index()
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $filter = new ResourcesFilter(Resources::query(), session('filters.resources'));
        $resources = $filter->filter()->whereNull('project_id')->orderBy('resource_code')->orderBy('name')->paginate(100);
        return view('resources.index', compact('resources'));
    }

    public function create()
    {
        if ($project_id = request('project')) {
            $project = Project::find($project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $units_drop = Unit::options();
        $partners = BusinessPartner::options();
        $resource_types = ResourceType::lists('name', 'id')->all();
        $edit = false;

        return view('resources.create', compact('partners', 'resource_types', 'units_drop', 'edit'));
    }

    public function store(Request $request)
    {
        if ($project_id = request('project')) {
            $project = Project::find($project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        }

        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }
        $this->validate($request, $this->rules);

//        if ($request['waste'] <= 1) {
        $request['waste'] = $request->waste;//updated from Eng.kareem 27/12/2016
//        } else {
//            $request['waste'] = ($request->waste / 100);
//        }
        $request['project_id'] = $request['project'];
        $newResource = Resources::create($request->all());

        flash('Resource has been saved', 'success');
        if ($newResource->project_id) {
            return \Redirect::route('project.show', $newResource->project_id);
        }
        return \Redirect::route('resources.index');
    }

    public function show(Resources $resource)
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('resources.show', compact('resource'));
    }

    public function edit(Resources $resources)
    {
        if ($resources->project_id) {
            $project = Project::find($resources->project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $partners = BusinessPartner::options();
        $resource_types = ResourceType::lists('name', 'id')->all();
        $units_drop = Unit::options();
        $edit = true;

        return view('resources.edit', compact('resources', 'partners', 'resource_types', 'units_drop', 'edit'));
    }

    public function update(Resources $resources, Request $request)
    {
        if ($resources->project_id) {
            $project = Project::find($resources->project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

//        $this->validate($request, $this->rules);
//        if ($request['waste'] <= 1) {
        $request['waste'] = $request->waste;
//        } else {
//            $request['waste'] = ($request->waste / 100);
//        }

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
        if ($resources->project_id) {
            $project = Project::find($resources->project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('delete', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $resources->delete();


        flash('Resources has been deleted', 'success');

        return \Redirect::route('resources.index');
    }

    function import()
    {
        if ($project_id = request('project')) {
            $project = Project::find($project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('resources.import');
    }

    function postImport(Request $request)
    {
        $project = null;
        if ($project_id = request('project')) {
            $project = Project::find($project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
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
            flash(nl2br("<strong>{$status['success']} Resources have been imported\n\nThe following items already exists</strong>\n" . implode("\n", $status['dublicated'])), 'info');
        } else {
            flash($status['success'] . ' Resources have been imported', 'success');
        }

        if ($project) {
            return redirect()->route('project.budget', $project);
        }
        return redirect()->route('resources.index');
    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('No data found');
            return \Redirect::route('resources.index');
        }

        $status = \Cache::get($key);

        if ($status['project']) {
            if (\Gate::denies('resources', $status['project'])) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }


        $items = $status['failed'];

        return view('resources.fix-import', compact('items', 'key'));
    }

    function postFixImport(Request $request, $key)
    {
        if (!\Cache::has($key)) {
            flash('No data found');
            return \Redirect::route('resources.index');
        }

        $status = \Cache::get($key);

        if ($status['project']) {
            if (\Gate::denies('resources', $status['project'])) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
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
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

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
        if (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

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
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $data = $request->only(['name', 'unit', 'resource_type_id', 'resource_code']);
        \Session::set('filters.resources', $data);

        return \Redirect::route('resources.index');
    }

    public function exportResources(Project $project)
    {
        if (\Gate::denies('resources', $project)) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return $this->dispatch(new ExportResourcesJob($project));
    }
    function exportCostResources(Project $project)
    {
        if (\Gate::denies('resources', $project)) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return $this->dispatch(new ExportCostResources($project));

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
        $project_id = request('project', null);
        if ($project_id) {
            $project = Project::find($project_id);
            if (cannot('resource_mapping', $project)) {
                flash("You are not authorized to do this action");
                return \Redirect::route('project.cost-control', $project);
            }
        } else {
            if (cannot('write', 'resources')) {
                flash("You are not authorized to do this action");
                return \Redirect::to('/project');
            }
        }

        return view('resources.import-codes');
    }

    function postImportCodes(Request $request)
    {
        $project_id = $request->get('project', null);
        if ($project_id) {
            $project = Project::find($project_id);
            if (cannot('resource_mapping', $project)) {
                flash("You are not authorized to do this action");
                return \Redirect::route('project.cost-control', $project);
            }
        } else {
            if (cannot('write', 'resources')) {
                flash("You are not authorized to do this action");
                return \Redirect::to('/');
            }
        }


        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $result = $this->dispatch(new ImportResourceCodesJob($file->path(), $project_id));

        if ($result['failed']->count()) {
            $key = 'res_codes_' . time();
            \Cache::put($key, $result, 180);
            flash('Could not import some resource codes', 'warning');
            return \Redirect::route('resources.fix-import-codes', $key);
        }

        flash($result['success'] . ' Equivalent codes have been imported successfully', 'success');
        if ($project_id) {
            return \Redirect::route('project.cost-control', $project_id);
        }
        return \Redirect::route('resources.index');
    }

    function fixImportCodes($key)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            \Redirect::to('/');
        }

        if ($data['project']) {
            if (cannot('resource_mapping', $data['project'])) {
                flash("You are not authorized to do this action");
                return \Redirect::route('project.cost-control', $data['project']);
            }
        } elseif (cannot('write', 'resources')) {
            flash("You are not authorized to do this action");
            return \Redirect::to('/resources');
        }

        $resourcesQuery = Resources::query();
        if ($data['project']) {
            $resourcesQuery->where('project_id', $data['project']->id);
        } else {
            $resourcesQuery->whereNull('project_id');
        }
        $data['resources'] = $resourcesQuery->with('types')->orderBy('name')->orderBy('resource_code')
            ->select('id', 'name', 'resource_code', 'resource_type_id')->get();

        return view('resources.fix-import-codes', $data);
    }

    function postFixImportCodes($key, Request $request)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            \Redirect::to('/');
        }

        if ($data['project']) {
            if (cannot('resource_mapping', $data['project'])) {
                flash("You are not authorized to do this action");
                return \Redirect::route('project.cost-control', $data['project']);
            }
        } elseif (cannot('write', 'resources')) {
            flash("You are not authorized to do this action");
            return \Redirect::to('/resources');
        }

        $resources = $request->get('mapping');
        $project_id = $data['project']->id ?? null;
        foreach ($resources as $code => $resource_id) {
            if ($resource_id) {
                ResourceCode::create(compact('code', 'resource_id', 'project_id'));
                ++$data['success'];
            }
        }

        flash("{$data['success']} Equivalent codes have been imported successfully", 'success');
        if ($project_id) {
            return \Redirect::route('project.cost-control', $project_id);
        }
        return \Redirect::route('resources.index');
    }

    public function exportAllResources()
    {
        if (\Gate::denies('read', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return $this->dispatch(new ExportPublicResourcesJob());
    }

    public function modifyAllResources()
    {
        $project_id = request('project');
        if ($project_id) {
            $project = Project::find($project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('resources.modify', ['project_id' => $project_id]);
//        return view('resources.modify');
    }

    public function postModifyAllResources(Request $request)
    {
        $project_id = $request->get('project');
        if ($project_id) {
            $project = Project::find($project_id);
            if (\Gate::denies('resources', $project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } elseif (\Gate::denies('write', 'resources')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $file = $request->file('file');
        $this->dispatch(new ModifyPublicResourcesJob($file, $project_id));

        flash('Modified resources have been imported', 'success');
        if ($project_id) {
            return \Redirect::route('project.budget', $project);
        }

        return \Redirect::route('resources.index');
    }

    function projectWipeAll(Project $project)
    {
        Resources::where('project_id', $project->id)->delete();
        \Cache::forget('resources-tree');
        return redirect()->route('project.show', $project);
    }

    function exportResourceMapping(Project $project){
        $this->dispatch(new ExportResourcesMapping($project));
    }
}
