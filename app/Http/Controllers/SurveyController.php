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

        $this->dispatch(new QuantitySurveyImportJob($project, $file->path()));

        flash('Quantity survey has been imported', 'success');
        return redirect()->route('project.show', $project);
    }
}
