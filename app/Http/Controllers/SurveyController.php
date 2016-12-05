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
        $surveys = Survey::paginate();
        return view('survey.index', compact('surveys'));
    }

    public function create(Request $request)
    {
        if (!$request->has('project')) {
            return \Redirect::route('project.index');
        }

        if (!Project::find($request->get('project'))) {
            return \Redirect::route('project.index');
        }

        return view('survey.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $level = WbsLevel::find($request->wbs_level_id);
        $level_survey = Survey::where('wbs_level_id', $level->id)->first();
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
            flash('Found dublicated Cost Account', 'danger');

        } else {
            $survey = Survey::create($request->all());
            flash('Quantity survey has been saved', 'success');
        }


        return \Redirect::to('/blank?reload=quantities');
    }

    public function show(Survey $survey)
    {
        return view('survey.show', compact('survey', 'units'));
    }

    public function edit(Survey $survey)
    {
        return view('survey.edit', compact('survey'));
    }

    public function update(Survey $survey, Request $request)
    {

        $this->validate($request, $this->rules);
        $survey->update($request->all());
        $survey->syncVariables($request->get('variables'));

        flash('Quantity survey has been saved', 'success');

        return \Redirect::to('/blank?reload=quantities');
    }

    public function destroy(Survey $survey, Request $request)
    {
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
        return view('survey.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
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
        } else {
            flash($status['success'] . ' Quantity survey items have been imported', 'success');
        }
        return \Redirect::to('/blank?reload=quantities');
    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return redirect()->route('project.index');
        }

        $status = \Cache::get($key);
        $project = Project::find($status['project_id']);
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