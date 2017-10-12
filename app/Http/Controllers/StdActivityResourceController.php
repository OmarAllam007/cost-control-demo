<?php

namespace App\Http\Controllers;

use App\Breakdown;
use App\BreakdownTemplate;
use App\Resources;
use App\StdActivityResource;
use Illuminate\Http\Request;

class StdActivityResourceController extends Controller
{

    protected $rules = ['template_id' => 'required', 'resource_id' => 'required', 'equation' => 'required'];

    public function create()
    {
        $template = BreakdownTemplate::find(request('template'));
        if (!$template) {
            flash('Breakdown template is not found');
            return \Redirect::route('breakdown_template.index');
        }

        if ($template->project) {
            if (\Gate::denies('breakdown_templates', $template->project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else {
            if (\Gate::denies('write', 'breakdown-template')) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        }

        return view('std-activity-resource.create');
    }

    public function store(Request $request)
    {
        $template = BreakdownTemplate::find(request('template_id'));
        if (!$template) {
            flash('Breakdown template is not found');
            return \Redirect::route('breakdown_template.index');
        }

        if ($template->project) {
            if (\Gate::denies('breakdown_templates', $template->project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else {
            if (\Gate::denies('write', 'breakdown-template')) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        }

        $this->validate($request, $this->rules);

        $resource = new StdActivityResource($request->all());

        if ($template->project) {
            \Session::put('template_resource', $resource);
            $hasBreakdowns = Breakdown::whereProjectId($template->project->id)->whereTemplateId($template->id)->exists();
            if ($hasBreakdowns) {
                return \Redirect::route('template-resource.create', [$template->project, $template]);
            }
        }

        $resource->save();

        flash('Template resource has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $resource->template);
    }

    public function show(StdActivityResource $std_activity_resource)
    {
        return view('std-activity-resource.show', compact('std_activity_resource'));
    }

    public function edit(StdActivityResource $std_activity_resource)
    {
        $template = $std_activity_resource->template;
        if ($template->project) {
            if (\Gate::denies('breakdown_templates', $template->project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else {
            if (\Gate::denies('write', 'breakdown-template')) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        }

        return view('std-activity-resource.edit', compact('std_activity_resource'));
    }

    public function update(StdActivityResource $std_activity_resource, Request $request)
    {
        $template = $std_activity_resource->template;
        if ($template->project) {
            if (\Gate::denies('breakdown_templates', $template->project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else {
            if (\Gate::denies('write', 'breakdown-template')) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        }

        $this->validate($request, $this->rules);

        $std_activity_resource->update($request->all());

        flash('Std activity resource has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $std_activity_resource->template);
    }

    public function destroy(StdActivityResource $std_activity_resource)
    {
        $template = $std_activity_resource->template;
        if ($template->project) {
            if (\Gate::denies('breakdown_templates', $template->project)) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        } else {
            if (\Gate::denies('write', 'breakdown-template')) {
                flash("You don't have access to this page");
                return \Redirect::to('/');
            }
        }

        $std_activity_resource->delete();

        flash('Std activity resource has been deleted', 'success');

        return \Redirect::route('breakdown-template.show', $std_activity_resource->template);
    }
}
