<?php

namespace App\Http\Controllers;

use App\Boq;
use App\Import\QtySurvey\QtySurveyFixer;
use Illuminate\Http\Request;


class FixQtySurveyBoqController extends Controller
{
    function create($key)
    {
        $cached = \Cache::get($key);

        $project = $cached['project'];
        $items = $cached['boqs'];
        $success = $cached['success'];
        $failed = $cached['failed'];

        $boqs = Boq::with('unit')->find($items->keys()->toArray())->keyBy('id');

        return view('survey.fix-boq', compact('project', 'items', 'success', 'boqs', 'failed'));
    }

    function store($key, Request $request)
    {
        $cached = \Cache::get($key);
        $project = $cached['project'];
        $items = $cached['boqs'];

        $this->validate($request, [
            'budget_qty.*' => 'required|gte:0', 'eng_qty.*' => 'required|gte:0',
        ]);

        $fixer = new QtySurveyFixer($project, $items, $request->only('budget_qty', 'eng_qty'));
        $fixer->fix();

        flash('Qty surveys have been saved', 'success');

        if (request('iframe')) {
            return \Redirect::to('/blank?reload=quantities');
        }

        return \Redirect::route('project.budget', $project);
    }
}
