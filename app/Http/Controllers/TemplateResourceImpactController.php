<?php

namespace App\Http\Controllers;

use App\Boq;
use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BreakdownTemplate;
use App\Formatters\BreakdownResourceFormatter;
use App\Project;
use App\Resources;
use App\TemplateResource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TemplateResourceImpactController extends Controller
{
    public function create(Project $project, BreakdownTemplate $breakdown_template)
    {
        if (cannot('breakdown_templates', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::to('/');
        }

        $template_resource = session('template_resource');

        $breakdowns = $this->buildCreateResource($project, $breakdown_template);

        return view('template-resource-impact.create', compact('project', 'breakdown_template', 'breakdowns', 'template_resource'));
    }

    public function store(Project $project, BreakdownTemplate $breakdown_template, Request $request)
    {
        if (cannot('breakdown_templates', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::to('/');
        }

        $resource = session('template_resource');

        $resource->save();

        Breakdown::with('wbs_level')
            ->where('project_id', $project->id)
            ->where('template_id', $breakdown_template->id)
            ->whereIn('id', array_keys($request->get('breakdown')))
            ->get()->each(function (Breakdown $breakdown) use ($resource) {
                $budget_qty = $breakdown->wbs_level->getBudgetQty($breakdown->cost_account);
                $eng_qty = $breakdown->wbs_level->getEngQty($breakdown->cost_account);
                $breakdown->resources()->create([
                    'std_activity_resource_id' => $resource->id, 'resource_id' => $resource->resource_id,
                    'budget_qty' => $budget_qty, 'eng_qty' => $eng_qty,
                    'equation' => $resource->equaiton, 'resource_waste' => $resource->resource_waste ?: 0,
                    'labor_count' => $resource->labor_count,
                    'remarks' => $resource->remarks, 'productivity_id' => $resource->productivity_id,
                ]);
            });

        flash('Template resource has been saved', 'success');

        return \Redirect::route('breakdown-template.show', $resource->template);
    }

    public function edit(Project $project, TemplateResource $template_resource)
    {
        if (cannot('breakdown_templates', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::to('/');
        }

        $new_template_resource = session('template_resource');

        $breakdown_resource_ids = BreakdownResource::where('std_activity_resource_id', $template_resource->id)->pluck('id');
        $resources = BreakDownResourceShadow::whereIn('breakdown_resource_id', $breakdown_resource_ids)
            ->with(['wbs', 'breakdown_resource'])
            ->get()->map(function ($shadow) use ($new_template_resource) {
                $new_breakdown_resource = clone $shadow->breakdown_resource;
                $new_breakdown_resource->std_activity_resource_id = $new_template_resource->id;
                $new_breakdown_resource->resource_id = $new_template_resource->resource_id;
                $new_breakdown_resource->equation = $new_template_resource->equation;
                $new_breakdown_resource->labor_count = $new_template_resource->labor_count;
                $new_breakdown_resource->productivity_id = $new_template_resource->productivity_id;

                $attributes = (new BreakdownResourceFormatter($new_breakdown_resource))->toArray();
                $shadow->new_shadow = new BreakDownResourceShadow($attributes);
                return $shadow;
            });

        return view('template-resource-impact.edit', compact('project', 'breakdown_template', 'resources', 'template_resource', 'new_template_resource'));
    }

    public function update(Request $request, Project $project, TemplateResource $template_resource)
    {
        if (cannot('breakdown_templates', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::to('/');
        }

        $new_template_resource = session('template_resource');

        $new_template_resource->save();

        BreakdownResource::where('std_activity_resource_id', $template_resource->id)
            ->whereIn('id', $request->get('resources'))
            ->get()->each(function ($resource) use ($new_template_resource) {
                $resource->std_activity_resource_id = $new_template_resource->id;
                $resource->resource_id = $new_template_resource->resource_id;
                $resource->equation = $new_template_resource->equation;
                $resource->labor_count = $new_template_resource->labor_count;
                $resource->productivity_id = $new_template_resource->productivity_id;

                $resource->save();
            });

        flash('Template resource has been updated', 'success');
        return \Redirect::route('breakdown-template.show', $template_resource->template);
    }

    public function delete(Project $project, TemplateResource $template_resource)
    {
        if (cannot('breakdown_templates', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::to('/');
        }

        $new_template_resource = session('template_resource');

        $breakdown_resource_ids = BreakdownResource::where('std_activity_resource_id', $template_resource->id)->pluck('id');
        $resources = BreakDownResourceShadow::whereIn('breakdown_resource_id', $breakdown_resource_ids)
            ->with(['wbs', 'breakdown_resource'])
            ->with('boq')
            ->get();

        return view('template-resource-impact.delete', compact('project', 'breakdown_template', 'resources', 'template_resource', 'new_template_resource'));
    }

    public function destroy(Request $request, Project $project, TemplateResource $template_resource)
    {
        if (cannot('breakdown_templates', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::to('/');
        }

        $template_resource->delete();

        BreakdownResource::where('std_activity_resource_id', $template_resource->id)
            ->whereIn('id', $request->get('resources'))
            ->with('boq')
            ->get()->each(function ($resource) {
                $resource->delete();
            });

        flash('Template resource has been deleted', 'success');
        return \Redirect::route('breakdown-template.show', $template_resource->template);
    }

    /**
     * @param Project $project
     * @param BreakdownTemplate $breakdown_template
     * @return Collection
     */
    protected function buildCreateResource(Project $project, BreakdownTemplate $breakdown_template)
    {
        $template_resource = session('template_resource');

        $resource = Resources::where('project_id', $project->id)
            ->where('resource_id', $template_resource->resource_id)
            ->first();

        if (!$resource) {
            $resource = Resources::find($template_resource->resource_id);
        }

        return $breakdowns = Breakdown::with('wbs_level')
            ->where('project_id', $project->id)
            ->where('template_id', $breakdown_template->id)
            ->get()->map(function ($breakdown) use ($template_resource, $resource) {
                $boq = Boq::costAccountOnWbs($breakdown->wbs_level, $breakdown->cost_account)->first();

                $new_resource = new BreakdownResource(['breakdown_id' => $breakdown->id]);
                $new_resource->equation = $template_resource->equation;
                $new_resource->resource = $resource;
                $new_resource->budget_qty = $new_resource->qty_survey->budget_qty ?? 0;
                $new_resource->eng_qty = $new_resource->qty_survey->eng_qty ?? 0;
                $new_resource->unit_price = $resource->rate;
                $new_resource->labor_count = $template_resource->labor_count;
                $new_resource->productivity_id = $template_resource->productivity_id;
                $breakdown->item_description = $boq->description ?? '';

                $breakdown->new_resource = $new_resource;
                return $breakdown;
            });
    }
}
