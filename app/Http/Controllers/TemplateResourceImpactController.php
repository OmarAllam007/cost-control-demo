<?php

namespace App\Http\Controllers;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakdownTemplate;
use App\Project;
use App\Resources;
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

        $breakdowns = $this->buildCreateResource($project, $breakdown_template);

        return view('template-resource-impact.create', compact('project', 'breakdown_template', 'breakdowns'));
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

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

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
                $new_resource = new BreakdownResource(['breakdown_id' => $breakdown->id]);
                $new_resource->equation = $template_resource->equation;
                $new_resource->resource = $resource;
                $new_resource->budget_qty = $new_resource->qty_survey->budget_qty ?? 0;
                $new_resource->eng_qty = $new_resource->qty_survey->eng_qty ?? 0;
                $new_resource->unit_price = $resource->rate;

                $breakdown->new_resource = $new_resource;
                return $breakdown;
            });
    }
}
