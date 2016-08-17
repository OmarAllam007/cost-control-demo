<?php

namespace App\Http\Controllers;

use App\Category;
use App\Survey;
use App\Unit;
use Illuminate\Http\Request;

class SurveyController extends Controller
{


    public function index()
    {

        $surveys = Survey::paginate();
        $units_drop = Unit::lists('type', 'id')->all();
        return view('survey.index', compact('surveys', 'units_drop', 'units'));
    }

    public function create()
    {
        $units_drop = Unit::lists('type', 'id')->all();
        $categories = Category::lists('name', 'id')->all();
        return view('survey.create', ['units_drop' => $units_drop
            ,'categories'=>$categories]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'budget_qty' => 'required',
            'eng_qty' => 'required'
        ]);
        Survey::create($request->all());

        flash('Survey has been saved', 'success');

        return \Redirect::route('survey.index');
    }

    public function show(Survey $survey)
    {
        $units = Unit::all();
        return view('survey.show', compact('survey', 'units'));
    }

    public function edit(Survey $survey)
    {
        $units = Unit::all();
        return view('survey.edit', compact('survey', 'units'));
    }

    public function update(Survey $survey, Request $request)
    {
        $this->validate($request, $this->rules);

        $survey->update($request->all());

        flash('Survey has been saved', 'success');

        return \Redirect::route('survey.index');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        flash('Survey has been deleted', 'success');

        return \Redirect::route('survey.index');
    }
}
