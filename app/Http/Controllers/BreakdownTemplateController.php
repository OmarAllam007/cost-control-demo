<?php

namespace App\Http\Controllers;

use App\BreakdownTemplate;
use App\Filter\BreakdownTemplateFilter;
use App\Jobs\ImportBreakdownTemplateJob;
use Illuminate\Http\Request;

class BreakdownTemplateController extends Controller
{

    protected $rules = ['name' => 'required', 'std_activity_id' => 'required|exists:std_activities,id'];

    public function index()
    {
        $filter = new BreakdownTemplateFilter(BreakdownTemplate::query(), session('filters.breakdown-template'));
        $breakdownTemplates = $filter->filter()->paginate(50);
        return view('breakdown-template.index', compact('breakdownTemplates'));
    }

    public function create()
    {
        return view('breakdown-template.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $template = BreakdownTemplate::create($request->all());

        flash('Breakdown template has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $template);
    }

    public function show(BreakdownTemplate $breakdown_template)
    {
        return view('breakdown-template.show', compact('breakdown_template'));
    }

    public function edit(BreakdownTemplate $breakdown_template)
    {
        return view('breakdown-template.edit', compact('breakdown_template'));
    }

    public function update(BreakdownTemplate $breakdown_template, Request $request)
    {
        $this->validate($request, $this->rules);

        $breakdown_template->update($request->all());

        flash('Breakdown template has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $breakdown_template);
    }

    public function destroy(BreakdownTemplate $breakdown_template)
    {
        $breakdown_template->delete();

        flash('Breakdown template has been deleted', 'success');

        return \Redirect::route('std-activity.show', $breakdown_template->activity);
    }

    function filters(Request $request)
    {
        $data = $request->only(['name', 'resource_id']);
        \Session::set('filters.breakdown-template', $data);
        return \Redirect::back();
    }

    function import()
    {
        return view('breakdown-template.import');
    }

    function postImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');
        $this->dispatch(new ImportBreakdownTemplateJob($file->path()));

        flash('Breakdown templates imported successfully', 'success');
        return \Redirect::route('breakdown-template.index');
    }
}
