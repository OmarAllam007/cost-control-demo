<?php

namespace App\Http\Controllers;

use App\BreakdownTemplate;
use App\Filter\BreakdownTemplateFilter;
use App\Jobs\ImportBreakdownTemplateJob;
use App\StdActivityResource;
use App\WbsLevel;
use Illuminate\Http\Request;

class BreakdownTemplateController extends Controller
{

    protected $rules = ['name' => 'required', 'std_activity_id' => 'required|exists:std_activities,id'];

    public function index()
    {
        if (\Gate::denies('read', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $filter = new BreakdownTemplateFilter(BreakdownTemplate::whereNull('project_id'), session('filters.breakdown-template'));
        $breakdownTemplates = $filter->filter()->paginate(75);
        return view('breakdown-template.index', compact('breakdownTemplates'));
    }

    public function create()
    {
        if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

//        $wbsTree = WbsLevel::tree()->get();
        return view('breakdown-template.create');
    }

    public function store(Request $request)
    {

        if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        if ($request->project_id && request('import')) {
            $parents = BreakdownTemplate::whereIn('id', $request->parent_template_id)->get();
            foreach ($parents as $parent) {
                $resources = StdActivityResource::where('template_id', $parent->id)->get();
                $parent->parent_template_id = $parent->id;
                $parent->project_id = $request->project_id;
                unset($parent->id);
                $template = BreakdownTemplate::create($parent->toArray());
                foreach ($resources as $resource) {
                    $resource->template_id = $template->id;
                    StdActivityResource::create($resource->toArray());
                }
            }

        } else {
            $this->validate($request, $this->rules);
            $template = BreakdownTemplate::create($request->all());
        }
        flash('Breakdown template has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $template);
    }

    public function show(BreakdownTemplate $breakdown_template)
    {
        if (\Gate::denies('read', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('breakdown-template.show', compact('breakdown_template'));
    }

    public function edit(BreakdownTemplate $breakdown_template)
    {
        if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('breakdown-template.edit', compact('breakdown_template'));
    }

    public function update(BreakdownTemplate $breakdown_template, Request $request)
    {
        if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        $breakdown_template->update($request->all());

        flash('Breakdown template has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $breakdown_template);
    }

    public function destroy(BreakdownTemplate $breakdown_template)
    {
        if (\Gate::denies('delete', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $breakdown_template->resources()->delete();
        $breakdown_template->delete();
        flash('Breakdown template has been deleted', 'success');

        $filter = new BreakdownTemplateFilter(BreakdownTemplate::whereNull('project_id'), session('filters.breakdown-template'));
        $breakdownTemplates = $filter->filter()->paginate(50);
        return \Redirect::back();
//        return view('breakdown-template.index', compact('breakdownTemplates'));
//        return \Redirect::route('std-activity.show', $breakdown_template->activity);
    }

    function filters(Request $request)
    {
        if (\Gate::denies('read', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $data = $request->only(['name', 'resource_id']);
        \Session::set('filters.breakdown-template', $data);
        return \Redirect::back();
    }

    function import()
    {
        if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('breakdown-template.import');
    }

    function postImport(Request $request)
    {
        if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');
        $this->dispatch(new ImportBreakdownTemplateJob($file->path()));

        flash('Breakdown templates imported successfully', 'success');
        return \Redirect::route('breakdown-template.index');
    }

    function deleteAll()
    {
        BreakdownTemplate::whereNull('project_id')->orWhereNotNull('deleted_at')->delete();
        flash('Breakdown templates Deleted successfully', 'success');
        return \Redirect::route('breakdown-template.index');
    }
}
