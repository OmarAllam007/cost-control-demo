<?php

namespace App\Http\Controllers;

use App\Boq;
use App\BoqDivision;
use App\Http\Requests\WipeRequest;
use App\Jobs\BoqImportJob;

use App\Jobs\Export\ExportBoqJob;
use App\Project;
use App\Unit;
use App\UnitAlias;
use App\WbsLevel;
use Illuminate\Http\Request;

class BoqController extends Controller
{

    protected $rules = ['project_id' => 'required', 'wbs_id' => 'required', 'cost_account' => 'required'];

    public function index()
    {
        $boqs = Boq::paginate();

        return view('boq.index', compact('boqs'));
    }

    public function create(Request $request)

    {
        if (!$request->has('project')) {
            flash('Project not found');
            return redirect()->route('project.index');
        } else {
            $project = Project::find($request->get('project'));
            if (!$project) {
                flash('Project not found');
                return redirect()->route('project.index');
            }
        }
        $wbsLevels = WbsLevel::tree()->paginate();

        return view('boq.create', compact('wbsLevels'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $boq = Boq::create($request->all());

        flash('Boq has been saved', 'success');
//        return \Redirect::route('project.show', $boq->project_id);
        return \Redirect::to('/blank?reload=boq');
    }


    public function show(Boq $boq)
    {
        return view('boq.show', compact('boq'));
    }

    public function edit(Boq $boq)
    {
        return view('boq.edit', compact('boq'));
    }

    public function update(Boq $boq, Request $request)
    {
        $this->validate($request, $this->rules);
        $boq->update($request->all());

        flash('Boq has been saved', 'success');
//        return \Redirect::route('project.show', $boq->project_id);
        return \Redirect::to('/blank?reload=boq');
    }

    public function destroy(Boq $boq, Request $request)
    {
        $boq->delete();

        $msg = 'Boq item has been deleted';
        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }

        flash($msg, 'success');
        return \Redirect::route('project.show', $boq->project_id);
    }

    public function deleteAll(Project $project)
    {
        $items = Boq::where('project_id', $project->id)->get();
        foreach ($items as $item) {
            $item->delete();
        }
        flash('All Boqs Deleted successfully', 'success');
        return \Redirect::route('project.show', $project->id);
    }

    function import(Project $project)
    {
        return view('boq.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file'//|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');
        $status = $this->dispatch(new BoqImportJob($project, $file->path()));
        if (count($status['failed'])) {
            $key = 'boq_' . time();
            \Cache::add($key, $status, 180);
            flash('Could not import all items', 'warning');
            return \Redirect::to(route('boq.fix-import', $key) . '?iframe=1');
        }

        flash($status['success'] . ' BOQ items have been imported', 'success');
//        return redirect()->route('project.show', $project);
        return \Redirect::to('/blank?reload=boq');
    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('project.index');
        }

        $status = \Cache::get($key);

        return view('boq.fix-import', ['items' => $status['failed'], 'project' => Project::find($status['project_id']), 'key' => $key]);
    }

    function postFixImport(Request $request, $key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('project.index');
        }

        $data = $request->get('data');
        $errors = Boq::checkFixImport($data);
        if (!$errors) {
            $status = \Cache::get($key);

            foreach ($status['failed'] as $item) {
                if (isset($item['orig_unit_id']) && isset($data['units'][$item['orig_unit_id']])) {
                    $item['unit'] = $data['units'][$item['orig_unit_id']];
                    UnitAlias::createAliasFor($item['unit'], $item['orig_unit_id']);
                }

                if (isset($item['orig_wbs_id']) && isset($data['wbs'][$item['orig_wbs_id']])) {
                    $item['wbs_id'] = $data['wbs'][$item['orig_wbs_id']];
                }

                Boq::create($item);
                ++$status['success'];
            }

            flash($status['success'] . ' BOQ items have been imported', 'success');
//            return \Redirect::to(route('project.show', $status['project_id']) . '#boq');
            return \Redirect::to('/blank?reload=boq');
        }

        flash('Could not import all items');
        return \Redirect::to(route('boq.fix-import', $key) . '?iframe=1')->withErrors($errors)->withInput($request->all());
    }

    function exportBoq(Project $project)
    {
        $this->dispatch(new ExportBoqJob($project));
    }

    function wipe(WipeRequest $request, Project $project)
    {
        $project->boqs()->delete();

        $msg = 'All BOQs have been deleted';

        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }

        flash($msg, 'info');
        return \Redirect::to(route('project.show', $project) . '#boq');
    }
}
