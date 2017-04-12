<?php

namespace App\Support;

use App\Boq;
use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BreakdownTemplate;
use App\Productivity;
use App\Project;
use App\Resources;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\Collection;

class DuplicateProject
{
    /** @var Project */
    protected $project;

    /** @var Project */
    protected $newProject;

    /** @var integer */
    protected $id;

    /** @var Collection */
    protected $resourcesMap;

    /** @var Collection */
    protected $stdActivityResourcesMap;

    /** @var Collection */
    protected $productivityMap;

    /** @var Collection */
    protected $wbsMap;

    /** @var Collection */
    protected $templatesMap;

    /** @var Collection */
    protected $breakdownResourcesMap;

    /** @var Collection */
    protected $breakdownsMap;

    function __construct(Project $project)
    {
        $this->project = $project;
        $this->wbsMap = collect();
        $this->stdActivityResourcesMap = collect();
        $this->breakdownResourcesMap = collect();
    }

    function duplicate($newName)
    {
        set_time_limit(1800);

        $start = microtime(1);
        $attributes = $this->project->getAttributes();
        $attributes['name'] = $newName;
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
        $this->newProject = Project::create($attributes);
        $this->id = $this->newProject->id;

        $this->duplicateWBS();
        $this->duplicateResources();
        $this->duplicateBreakdownTemplates();
        $this->duplicateProductivity();
        $this->duplicateBOQ();
        $this->duplicateQS();
        $this->duplicateBreakdown();
        $this->duplicateShadow();
        \Log::info(round(microtime(1) - $start, 4) . 's');
        return $this->id;
    }

    /**
     * @return Collection
     */
    protected function duplicateResources()
    {
        Resources::flushEventListeners();
        return $this->resourcesMap = $this->project->resources->keyBy('id')->map(function (Resources $resource) {
            $attributes = $resource->getAttributes();
            $attributes['project_id'] = $this->id;
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

            return Resources::create($attributes)->id;
        });
    }

    protected function duplicateBreakdownTemplates()
    {
        $templates = BreakdownTemplate::withTrashed()->with('resources')->where('project_id', $this->project->id)->get()->keyBy('id');
        $this->templatesMap = $templates->map(function (BreakdownTemplate $template) {
            $attributes = $template->getAttributes();
            $attributes['project_id'] = $this->id;
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
            $newTemplate = BreakdownTemplate::create($attributes);

            /** @var StdActivityResource $resource */
            foreach ($template->resources as $resource) {
                $attributes = $resource->getAttributes();
                if ($this->resourcesMap->has($resource->resource_id)) {
                    $attributes['resource_id'] = $this->resourcesMap->get($resource->resource_id);
                }
                unset($attributes['id'], $attributes['breakdown_template_id'], $attributes['created_at'], $attributes['created_at']);
                $this->stdActivityResourcesMap->put($resource->id, $newTemplate->resources()->create($attributes)->id);
            }

            return $newTemplate->id;
        });
    }

    protected function duplicateProductivity()
    {
        return $this->productivityMap = Productivity::where('project_id', $this->project->id)->get()->keyBy('id')->each(function (Productivity $productivity) {
            $attributes = $productivity->getAttributes();
            $attributes['project_id'] = $this->id;
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

            return Productivity::create($attributes)->id;
        });
    }

    protected function duplicateWBS($parent_id = 0)
    {
        WbsLevel::where('project_id', $this->project->id)
            ->where('parent_id', $parent_id)->get()
            ->each(function (WbsLevel $level) use ($parent_id) {
                $attributes = $level->getAttributes();
                $attributes['project_id'] = $this->newProject->id;
                $attributes['parent_id'] = $this->wbsMap->get($parent_id, 0);
                unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

                $id = WbsLevel::create($attributes)->id;
                $this->wbsMap->put($level->id, $id);
                $this->duplicateWBS($level->id);
            });
    }

    protected function duplicateBreakdown()
    {
        Breakdown::flushEventListeners();
        BreakdownResource::flushEventListeners();
        $this->breakdownsMap = Breakdown::with('resources')->where('project_id', $this->project->id)
            ->get()->keyBy('id')->map(function (Breakdown $breakdown) {
                $attributes = $breakdown->getAttributes();
                $attributes['project_id'] = $this->id;
                $attributes['wbs_level_id'] = $this->wbsMap->get($breakdown->wbs_level_id);
                if ($this->templatesMap->has($breakdown->template_id)) {
                    $attributes['template_id'] = $this->templatesMap->get($breakdown->template_id);
                }
                unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

                $newBreakdown = Breakdown::create($attributes);
                $breakdown->resources->each(function (BreakdownResource $resource) use ($newBreakdown) {
                    $attributes = $resource->getAttributes();
                    unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
                    if ($this->stdActivityResourcesMap->has($resource->std_activity_resource_id)) {
                        $attributes['std_activity_resource_id'] = $this->stdActivityResourcesMap->get($resource->std_activity_resource_id);
                    }

                    if ($this->resourcesMap->has($resource->resource_id)) {
                        $attributes['resource_id'] = $this->resourcesMap->get($resource->resource_id);
                    }

                    if ($this->resourcesMap->has($resource->productivity_id)) {
                        $attributes['productivity_id'] = $this->productivityMap->get($resource->productivity_id);
                    }

                    $this->breakdownResourcesMap->put($resource->id, $newBreakdown->resources()->create($attributes)->id);
                });

                $variables = $breakdown->variables()->pluck('value', 'display_order');
                $newBreakdown->syncVariables($variables);

                return $newBreakdown->id;
            });
    }

    protected function duplicateShadow()
    {
        BreakDownResourceShadow::flushEventListeners();
        BreakDownResourceShadow::where('project_id', $this->project->id)->get()->each(function (BreakDownResourceShadow $resource) {
            $attributes = $resource->getAttributes();
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

            $attributes['project_id'] = $this->id;
            $attributes['breakdown_resource_id'] = $this->breakdownResourcesMap->get($resource->breakdown_resource_id);
            $attributes['wbs_id'] = $this->wbsMap->get($resource->wbs_id);
            $attributes['breakdown_id'] = $this->breakdownsMap->get($resource->breakdown_id);
            if ($this->templatesMap->has($resource->template_id)) {
                $attributes['template_id'] = $this->templatesMap->get($resource->template_id);
            }

            $attributes['progress'] = 0;
            $attributes['status'] = '';

            if ($this->stdActivityResourcesMap->has($resource->std_activity_resource_id)) {
                $attributes['std_activity_resource_id'] = $this->stdActivityResourcesMap->get($resource->std_activity_resource_id);
            }

            if ($this->resourcesMap->has($resource->resource_id)) {
                $attributes['resource_id'] = $this->resourcesMap->get($resource->resource_id);
            }

            if ($this->resourcesMap->has($resource->productivity_id)) {
                $attributes['productivity_id'] = $this->productivityMap->get($resource->productivity_id);
            }

            BreakDownResourceShadow::create($attributes);
        });
    }

    protected function duplicateBOQ()
    {
        Boq::flushEventListeners();
        Boq::where('project_id', $this->project->id)->get()->map(function (Boq $boq) {
            $attributes = $boq->getAttributes();
            $attributes['project_id'] = $this->id;
            $attributes['wbs_id'] = $this->wbsMap->get($boq->wbs_id);
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

            Boq::create($attributes);
        });
    }

    protected function duplicateQS()
    {
        Survey::flushEventListeners();
        Survey::where('project_id', $this->project->id)->get()->map(function (Survey $survey) {
            $attributes = $survey->getAttributes();
            $attributes['project_id'] = $this->id;
            $attributes['wbs_level_id'] = $this->wbsMap->get($survey->wbs_level_id);
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

            Survey::create($attributes);
        });
    }
}