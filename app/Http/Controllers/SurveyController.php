<?php

namespace App\Http\Controllers;

use App\Category;
use App\Jobs\QuantitySurveyImportJob;
use App\Project;
use App\Survey;
use App\Unit;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    protected $rules = [
        'description' => 'required', 'cost_account' => 'required',
        'project_id' => 'required', 'wbs_level_id' => 'required',
        'budget_qty' => 'required', 'eng_qty' => 'required'
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

        $survey = Survey::create($request->all());

        flash('Quantity survey has been saved', 'success');

        return \Redirect::route('project.show', $survey->project_id);
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

        flash('Quantity survey has been saved', 'success');

        return \Redirect::route('project.show', $survey->project_id);
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        flash('Quantity survey has been deleted', 'success');

        return \Redirect::route('project.show', $survey->project_id);
    }

    function import(Project $project)
    {
        return view('survey.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $result = $this->dispatch(new QuantitySurveyImportJob($project, $file->path()));

        if ($result) {
            $key = 'qs_import_' . time();
            \Cache::add($key, $result, 180);
            flash('We could not import the following items. Please fix.', 'warning');
            return redirect()->route('survey.fix-import', $key);
        }

        flash('Quantity survey has been imported', 'success');
        return redirect()->route('project.show', $project);
    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return redirect()->route('survey.import');
        }

        $result = \Cache::get($key);
        $project = Project::find($result['project_id']);
        $items = $result['failed'];
        return view('survey.fix-import', compact('items', 'key', 'project'));
    }

    function postFixImport($key)
    {

    }
}
