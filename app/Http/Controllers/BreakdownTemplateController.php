<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
use App\Breakdown;
use App\BreakdownTemplate;
use App\Filter\BreakdownTemplateFilter;
use App\Jobs\ImportBreakdownTemplateJob;
use App\Project;
use App\StdActivity;
use App\StdActivityResource;
use App\Support\ActivityDivisionTree;
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

//        $filter = new BreakdownTemplateFilter(BreakdownTemplate::whereNull('project_id'), session('filters.breakdown-template'));
//        $breakdownTemplates = $filter->filter()->paginate(75);
        $divisions = (new ActivityDivisionTree())->get();

        return view('breakdown-template.index', compact('divisions'));
    }

    public function create()
    {
        if ($project_id = request('project')) {
            if (\Gate::denies('breakdown_templates', Project::find($project_id))) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('breakdown-template.create');
    }

    public function store(Request $request)
    {
        if ($project_id = request('project_id')) {
            if (\Gate::denies('breakdown_templates', Project::find($project_id))) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);
        $template = BreakdownTemplate::create($request->all());

        flash('Breakdown template has been saved', 'success');
        return \Redirect::route('breakdown-template.show', $template);
    }

    public function show(BreakdownTemplate $breakdown_template)
    {
        if ($breakdown_template->project) {
            if (\Gate::denies('breakdown_templates', $breakdown_template->project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else if (\Gate::denies('read', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }


        return view('breakdown-template.show', compact('breakdown_template'));
    }

    public function edit(BreakdownTemplate $breakdown_template)
    {
        if ($breakdown_template->project) {
            if (\Gate::denies('breakdown_templates', $breakdown_template->project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        return view('breakdown-template.edit', compact('breakdown_template'));
    }

    public function update(BreakdownTemplate $breakdown_template, Request $request)
    {
        if ($breakdown_template->project) {
            if (\Gate::denies('breakdown_templates', $breakdown_template->project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else if (\Gate::denies('write', 'breakdown-template')) {
            flash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $this->validate($request, $this->rules);

        $breakdown_template->update($request->all());

        flash('Breakdown template has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $breakdown_template);
    }

    public function destroy(BreakdownTemplate $breakdown_template, Request $request)
    {
        if ($breakdown_template->project) {
            $this->authorize('breakdown_templates', $breakdown_template->project);
        } else {
            $this->authorize('write', 'breakdown-template');
        }

        $breakdown_template->resources()->delete();
        $breakdown_template->delete();

        if ($request->wantsJson()) {
            return ['ok' => true];
        }

        flash('Breakdown template has been deleted', 'success');
        return \Redirect::back();
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
//
//        $this->validate($request, [
//            'file' => 'required|file|mimes:xls,xlsx',
//        ]);

        $file = $request->file('file');
        $count = $this->dispatch(new ImportBreakdownTemplateJob($file->path()));


        flash($count.' Breakdown templates imported successfully', 'success');
        return \Redirect::route('breakdown-template.index');
    }

    function deleteAll()
    {
        if (!\Gate::denies('wipe')) {
            lash("You don't have access to this page");
            return \Redirect::to('/');
        }

        $templates = BreakdownTemplate::whereNull('project_id')->get();
        foreach ($templates as $template){
            StdActivityResource::where('template_id',$template->id)->delete();
            $template->delete();
        }

        flash('Breakdown templates Deleted successfully', 'success');
        return \Redirect::route('breakdown-template.index');
    }
}
