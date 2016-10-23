<?php

namespace App\Http\Controllers;

use App\BusinessPartner;
use App\Filter\ResourcesFilter;
use App\Http\Requests\WipeRequest;
use App\Jobs\ResourcesImportJob;
use App\Project;
use App\Resources;
use App\ResourceType;
use App\StdActivityResource;
use App\Unit;
use Illuminate\Http\Request;

class ResourcesController extends Controller
{

    protected $rules = ['name' => 'required'];

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
        $resources = Resources::all();
        $resource_types = ResourceType::lists('name', 'id')->all();
        $edit = false;

        return view('resources.create', compact('partners', 'resources', 'resource_types', 'units_drop', 'edit'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        if ($request['waste'] <= 1) {
            $request['waste'] = $request->waste;
        } else {
            $request['waste'] = ($request->waste / 100);
        }

        Resources::create($request->all());

        flash('Resource has been saved', 'success');

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

        $this->validate($request, $this->rules);

        if ($request['waste'] <= 1) {
            $request['waste'] = $request->waste;
        } else {
            $request['waste'] = ($request->waste / 100);
        }

        $resources->update($request->all());

        flash('Resource has been saved', 'success');

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
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $failed = $this->dispatch(new ResourcesImportJob($file->path()));

        if ($failed) {
            $key = 'res_' . time();
            \Cache::add($key, ['items' => $failed], 180);
            flash('Could not import some resources', 'warning');
            return redirect()->route('resources.fix-import', $key);
        }

        flash('Resources have been imported', 'success');
        return redirect()->route('resources.index');
    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('resources.index');
        }

        $failed = \Cache::get($key);
        $items = $failed['items'];

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
        $failed = \Cache::get($key);

        if (!$errors) {
            $units = $data['units'];

            foreach ($failed['items'] as $item) {
                if (isset($units[$item['orig_unit']])) {
                    $item['unit'] = $units[$item['orig_unit']];
                    Resources::create($item);
                }
            }

            flash('Resources have been imported', 'success');
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
            $newResource->project_id = $project->id;
            $newResource->resource_id = $resources->id;
            $newResource->save();
        } else {
            $newResource->update($request->all());
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
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Code');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Resource Name');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Type');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Rate');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Unit');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Waste');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'reference');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Business Partner');
        $rowCount = 2;
        foreach ($project->plain_resources as $resource) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $resource->resource_code);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $resource->name);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $resource->types->root->name);

            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $resource->versionFor($project->id)->rate);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, isset($resource->versionFor($project->id)->units->type) ? $resource->versionFor($project->id)->units->type : '');

            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $resource->versionFor($project->id)->waste . '%');

            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $resource->reference);
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, isset(BusinessPartner::find($resource->business_partner_id)->name) ? BusinessPartner::find($resource->business_partner_id)->name : '');
            $rowCount++;

        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $project->name . ' - Resources.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

    function wipe(WipeRequest $request)
    {
        \DB::table('resources')->delete();
        \DB::table('std_activity_resources')->delete();
        \DB::table('resource_types')->delete();
//        StdActivityResource::query()->delete();

        flash('All resources have been deleted', 'info');

        return \Redirect::route('resources.index');
    }
}
