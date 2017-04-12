<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\WipeRequest;
use App\Jobs\Export\ExportSurveyJob;
use App\Jobs\QuantitySurveyImportJob;
use App\Project;
use App\Survey;
use App\Unit;
use App\UnitAlias;
use App\WbsLevel;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    protected $rules = [
        'description' => 'required', 'cost_account' => 'required',
        'project_id' => 'required', 'wbs_level_id' => 'required',
        'budget_qty' => 'required', 'eng_qty' => 'required',
    ];

    public function index()
    {
        abort(404);
        $surveys = Survey::paginate();
        return view('survey.index', compact('surveys'));
    }

    public function create(Request $request)
    {
        if (!$request->has('project')) {
            return \Redirect::route('project.index');
        }

        $project = Project::find($request->get('project'));
        if (!$project) {
            return \Redirect::route('project.index');
        }

        if (\Gate::denies('qty_survey', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        return view('survey.create');
    }

    public function store(Request $request)
    {
        if (!$request->has('project_id')) {
            return \Redirect::route('project.index');
        }

        $project = Project::find($request->get('project_id'));
        if (!$project) {
            return \Redirect::route('project.index');
        }

        if (\Gate::denies('qty_survey', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $this->validate($request, $this->rules);
        $level = WbsLevel::find($request->wbs_level_id);
        $level_survey = Survey::where('wbs_level_id', $level->id)->first();
        $cost_accounts = [];
        if ($level_survey) {
            $cost_accounts[] = $level_survey->cost_account;
        }
        $parent = $level;
        while ($parent->parent) {
            $parent = $parent->parent;
            $parent_survey = Survey::where('wbs_level_id', $parent->id)->first();
            if ($parent_survey) {
                $cost_accounts[] = $parent_survey->cost_account;
            }
        }

        if (in_array($request->cost_account, $cost_accounts)) {
            flash('Found dublicated cost account', 'danger');

        } else {
            $survey = Survey::create($request->all());
            flash('Quantity survey has been saved', 'success');
        }


        return \Redirect::to('/blank?reload=quantities');
    }

    public function show(Survey $survey)
    {
        abort(404);
        return view('survey.show', compact('survey', 'units'));
    }

    public function edit(Survey $survey)
    {
        if (\Gate::denies('qty_survey', $survey->project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }
        return view('survey.edit', compact('survey'));
    }

    public function update(Survey $survey, Request $request)
    {
        if (\Gate::denies('qty_survey', $survey->project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $this->validate($request, $this->rules);
        $survey->syncVariables($request->get('variables'));
        $survey->update($request->all());

        flash('Quantity survey has been saved', 'success');

        return \Redirect::to('/blank?reload=quantities');
    }

    public function destroy(Survey $survey, Request $request)
    {
        if (\Gate::denies('qty_survey', $survey->project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $survey->delete();

        $msg = 'Quantity survey has been deleted';
        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }
        flash($msg, 'success');
        return \Redirect::route('project.show', $survey->project_id);
    }

    function import(Project $project)
    {
        if (\Gate::denies('qty_survey', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        return view('survey.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        if (\Gate::denies('qty_survey', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $this->validate($request, [
            'file' => 'required|file'//|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $status = $this->dispatch(new QuantitySurveyImportJob($project, $file->path()));

        if ($status['failed']->count()) {
            $key = 'qs_import_' . time();
            \Cache::add($key, $status, 180);
            flash('Could not import some items.', 'warning');
            return redirect()->to(route('survey.fix-import', $key) . '?iframe=1');
        }

        if (count($status['dublicated'])) {
            $dublicatedKey = 'qs-dublicateded';
            \Cache::add($dublicatedKey, $status, 60);
            flash('Dublicated Cost Accounts', 'warning');

            return redirect()->to(route('survey.dublicate', $dublicatedKey) . '?iframe=1');
        } else {
            flash($status['success'] . ' Quantity survey items have been imported', 'success');
            return \Redirect::to('/blank?reload=quantities');
        }
    }

    function dublicateQuantitySurvey($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return redirect()->route('project.index');
        }
        $status = \Cache::get($key);
        $project = Project::find($status['project_id']);
        $dublicated_items = $status['dublicated'];
        return view('survey.dublicated', compact('dublicated_items', 'project', 'key'));
    }

    function postDublicateQuantitySurvey($key, Request $request)
    {

        $status = \Cache::get($key);
        $project = Project::find($status['project_id']);
        $data = $request->all();
        $levels = $data['data'];

        foreach ($levels as $costKey => $accounts) {
            foreach ($accounts as $lkey => $level) {
                $wbsLevel = WbsLevel::where('id', $level)->first();
                $check = $wbsLevel->getCostAccountCheck($wbsLevel, $lkey);
                if ($check) {
                    flash('Dublicated Exists', 'danger');
                    return \Redirect::back()->withInput(compact('data'));
                } else {
                    foreach ($status['dublicated']->toArray() as $ikey => $items) {
                        foreach ($items['wbs'] as $iKey => $item) {
                            $items['wbs_level_id'] = $level;
                            $project->quantities()->create($items);
                            \Cache::forget($key);
                            flash('Dublicated Fixed', 'success');
                            return \Redirect::to('/blank?reload=quantities');
                        }
                    }
                }
            }
        }

    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return redirect()->route('project.index');
        }

        $status = \Cache::get($key);
        $project = Project::find($status['project_id']);
        if (\Gate::denies('qty_survey', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $items = $status['failed'];
        return view('survey.fix-import', compact('items', 'key', 'project'));
    }

    function postFixImport($key, Request $request)
    {

        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return redirect()->route('project.index');
        }

        $status = \Cache::get($key);
        $project = Project::find($status['project_id']);
        if (\Gate::denies('qty_survey', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $data = $request->get('data');
        $errors = Survey::checkImportData($data);
        if (!$errors) {
            /** @var Project $project */
            $units = $data['units'];
            $wbs = $data['wbs'];
            foreach ($status['failed'] as $key => $item) {
                if (!$item['unit_id']) {
                    $item['unit_id'] = $units[$item['unit']];
                    UnitAlias::createAliasFor($item['unit_id'], $item['unit']);
                }

                if (!$item['wbs_level_id']) {
                    $item['wbs_level_id'] = $wbs[$item['wbs_code']];
                }

                $project->quantities()->create($item);
                ++$status['success'];
            }

            flash($status['success'] . ' Quantity survey items have been imported', 'success');
            return \Redirect::to('/blank?reload=quantities');
        }

        flash('Could not import some items.', 'warning');
        return \Redirect::back()->withErrors($errors)->withInput(compact('data'));
    }


    public function exportQuantitySurvey(Project $project)
    {
        if (\Gate::denies('budget', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $this->dispatch(new ExportSurveyJob($project));
    }


    function wipe(WipeRequest $request, Project $project)
    {
        $project->quantities()->delete();

        $msg = 'All quantities have been deleted';

        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }

        flash($msg, 'info');
        return \Redirect::to(route('project.show', $project) . '#quantity-survey');
    }

}