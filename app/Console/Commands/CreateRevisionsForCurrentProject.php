<?php

namespace App\Console\Commands;

use App\Breakdown;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\BudgetRevision;
use App\Project;
use App\Revision\RevisionBoq;
use App\Revision\RevisionBreakdown;
use App\Revision\RevisionBreakdownResource;
use App\Revision\RevisionBreakdownResourceShadow;
use App\Revision\RevisionProductivity;
use App\Revision\RevisionQtySurvey;
use App\Revision\RevisionResource;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CreateRevisionsForCurrentProject extends Command
{
    protected $signature = 'project:create-revisions';

    protected $description = 'Create revisions for current projects';

    /** @var Collection */
    protected $boqMap;

    /** @var Collection */
    protected $qtySurveyMap;

    /** @var Collection */
    protected $breakdownResourceMap;

    /** @var Collection */
    private $breakdownMap;

    /** @var Collection */
    private $resourcesMap;

    /** @var Collection */
    private $productivityMap;

    public function handle()
    {
        BudgetRevision::truncate();
        RevisionBreakdown::truncate();
        RevisionBreakdownResource::truncate();
        RevisionBoq::truncate();
        RevisionQtySurvey::truncate();

        Project::all()->filter(function (Project $project) {
            return !$project->revisions()->exists();
        })->each(function (Project $project) {
            $start = microtime(1);
            $this->output->title("Creating revisions for {$project->name}");
            $revision = $project->revisions()->create([
                'name' => 'rev_00'
            ]);

            $params = ['rev' => $revision->id, 'project' => $project->id];

            $this->output->note('Copying breakdowns');
            \DB::insert("INSERT INTO revision_breakdowns(breakdown_id, revision_id, wbs_level_id, project_id, template_id, std_activity_id, cost_account, `code`, created_by, updated_by, created_at, updated_at)
                  SELECT id AS breakdown_id, :rev revision_id, wbs_level_id, project_id, template_id, std_activity_id, cost_account, `code`, 2 AS created_by, 2 AS updated_by, now() AS created_at, now() AS updated_at FROM breakdowns WHERE project_id = :project", $params);

            $this->output->note('Copying BOQs');
            \DB::insert("INSERT INTO revision_boqs(boq_id, revision_id, wbs_id, item, description, type, unit_id, quantity, dry_ur, price_ur, arabic_description, division_id, `code`, item_code, cost_account, kcc_qty, subcon, materials, manpower, project_id, created_by, updated_by, created_at, updated_at) 
                  SELECT id AS boq_id, :rev revision_id, wbs_id, item, description, type, unit_id, quantity, dry_ur, price_ur, arabic_description, division_id, `code`, item_code, cost_account, kcc_qty, subcon, materials, manpower, project_id, 2 AS created_by, 2 AS updated_by, now() AS created_at, now() AS updated_at FROM boqs WHERE project_id = :project", $params);

            $this->output->note('Copying Qty Surveys');
            \DB::insert("INSERT INTO revision_qty_surveys(revision_id, qty_survey_id, cost_account, description, unit_id, budget_qty, eng_qty, deleted_at, wbs_level_id, project_id, `code`, discipline, created_by, updated_by, created_at, updated_at) 
                  SELECT :rev AS revision_id, id AS qty_survey_id, cost_account, description, unit_id, budget_qty, eng_qty, deleted_at, wbs_level_id, project_id, `code`, discipline, 2 created_by, 2 updated_by, now() AS created_at, now() AS updated_at FROM qty_surveys WHERE project_id = :project", $params);

            $this->output->note('Copying Resources');
            \DB::insert("INSERT INTO revision_resources(original_id, revision_id, resource_type_id, resource_code, name, rate, unit, waste, reference, business_partner_id, project_id, resource_id, top_material, created_by, updated_by, created_at, updated_at)  
                  SELECT id as original_id, :rev as revision_id, resource_type_id, resource_code, name, rate, unit, waste, reference, business_partner_id, project_id, resource_id, top_material, 2 AS created_by, 2 AS updated_by, now() AS created_at, now() AS updated_at FROM resources WHERE project_id = :project", $params);

            $this->output->note('Copying Productivities');
            \DB::insert("INSERT INTO revision_productivities(original_id, revision_id, project_id, csi_code, csi_category_id, description, unit, crew_structure, crew_hours, crew_equip, daily_output, man_hours, equip_hours, reduction_factor, after_reduction, source, code, productivity_id, created_by, updated_by, created_at, updated_at)   
                  SELECT id as original_id, :rev as revision_id, project_id, csi_code, csi_category_id, description, unit, crew_structure, crew_hours, crew_equip, daily_output, man_hours, equip_hours, reduction_factor, after_reduction, source, code, productivity_id, 2 AS created_by, 2 AS updated_by, now() AS created_at, now() AS updated_at FROM productivities where project_id = :project", $params);

            $this->output->note('Copying breakdown resources');
            $this->breakdownMap = RevisionBreakdown::where('project_id', $project->id)->pluck('id', 'breakdown_id');
            $this->resourcesMap = RevisionResource::where('project_id', $project->id)->pluck('id', 'original_id');
            $this->productivityMap = RevisionProductivity::where('project_id', $project->id)->pluck('id', 'original_id');
            $this->boqMap = RevisionBoq::where('project_id', $project->id)->pluck('id', 'boq_id');
            $this->qtySurveyMap = RevisionQtySurvey::where('project_id', $project->id)->pluck('id', 'qty_survey_id');

            $bar = $this->output->createProgressBar(ceil(BreakdownResource::whereRaw('breakdown_id in (select id from breakdowns where project_id = ?)', [$project->id])->count() / 950));
            BreakdownResource::whereRaw('breakdown_id in (select id from breakdowns where project_id = ?)', [$project->id])->chunk(950, function (Collection $resources) use ($bar) {
                $now = Carbon::now()->format('Y-m-d H:i:s');
                $newResources = $resources->map(function (BreakdownResource $r) use ($now) {
                    $attributes = $r->getAttributes();
                    $attributes['breakdown_resource_id'] = $attributes['id'];
                    $attributes['breakdown_id'] = $this->breakdownMap->get($attributes['breakdown_id']);
                    $attributes['resource_id'] = $this->resourcesMap->get($attributes['resource_id']);
                    $attributes['productivity_id'] = $this->productivityMap->get($attributes['productivity_id']);
                    $attributes['created_by'] = $attributes['updated_by'] = 2;
                    $attributes['created_at'] = $attributes['updated_at'] = $now;
                    unset($attributes['id']);
                    return $attributes;
                });

                \DB::transaction(function() use ($newResources, $bar) {
                    RevisionBreakdownResource::insert($newResources->toArray());
                    $bar->advance();
                });
            });
            $bar->finish();
            $this->output->newLine(2);

            $this->output->note('Copying breakdown shadow');
            $this->breakdownResourceMap = RevisionBreakdownResource::whereRaw('breakdown_id in (select id from revision_breakdowns where project_id = ?)', [$project->id])->pluck('id', 'breakdown_resource_id');
            $bar = $this->output->createProgressBar(ceil(BreakDownResourceShadow::where('project_id', $project->id)->count() / 950));
            BreakDownResourceShadow::where('project_id', $project->id)->chunk(950, function(Collection $collection) use ($bar) {
                $now = Carbon::now()->format('Y-m-d H:i:s');
                $new = $collection->map(function(BreakDownResourceShadow $r) use ($now) {
                    $attributes = $r->getAttributes();
                    $attributes['breakdown_id'] = $this->breakdownMap->get($attributes['breakdown_id']);
                    $attributes['breakdown_resource_id'] = $this->breakdownResourceMap->get($attributes['breakdown_resource_id']);
                    $attributes['resource_id'] = $this->resourcesMap->get($attributes['resource_id']);
                    $attributes['productivity_id'] = $this->productivityMap->get($attributes['productivity_id']);
                    $attributes['boq_id'] = $this->boqMap->get($attributes['boq_id']);
                    $attributes['survey_id'] = $this->qtySurveyMap->get($attributes['survey_id']);
                    $attributes['created_by'] = $attributes['updated_by'] = 2;
                    $attributes['created_at'] = $attributes['updated_at'] = $now;
                    unset($attributes['id']);
                    return $attributes;
                });

                \DB::transaction(function() use ($new, $bar) {
                    RevisionBreakdownResourceShadow::insert($new->toArray());
                    $bar->advance();
                });
            });
            $bar->finish();
            $this->output->newLine(2);

            $time = round(microtime(1) - $start, 4);

            $this->output->success("Completed in {$time}s");
        });

    }
}
