<?php

namespace App\Http\Controllers;

use App\BreakdownTemplate;
use App\Project;
use App\StdActivityResource;
use Illuminate\Http\Request;

class ImportTemplateToProjectController extends Controller
{
    function create(Project $project)
    {
        $this->authorize('breakdown_templates', $project);
        return view('breakdown-template.import-to-project', compact('project'));
    }

    function store(Project $project, Request $request)
    {
        $this->authorize('breakdown_templates', $project);

        $parents = BreakdownTemplate::whereIn('id', $request->get('templates'))->get();

        foreach ($parents as $parent) {
            $attributes = $parent->getAttributes();
            $attributes['parent_template_id'] = $parent->id;
            $attributes['project_id'] = $project->id;
            unset($attributes['id']);
            $template = BreakdownTemplate::create($attributes);

            foreach ($parent->resources as $resource) {
                $resourceAttributes = $resource->getAttributes();
                unset($resourceAttributes['id']);
                $resourceAttributes['template_id'] = $template->id;
                StdActivityResource::create($resourceAttributes);
            }
        }

        flash('Templates have been imported', 'success');
        if ($request->exists('iframe')) {
            return redirect('/blank?reload=templates');
        }
        return \Redirect::route('project.show', $project);
    }
}
