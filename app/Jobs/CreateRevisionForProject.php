<?php

namespace App\Jobs;

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
use App\User;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class CreateRevisionForProject extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    //<editor-fold defaultstate="collapsed" desc="Variable declaration">
    /** @var Project */
    public $project;

    /** @var User */
    public $user;

    /** @var Collection */
    protected $resourcesMap;

    /** @var Collection */
    protected $productivityMap;

    /** @var Collection */
    protected $boqMap;

    /** @var Collection */
    protected $qtySurveyMap;

    /** @var Collection */
    protected $breakdownResourceMap;

    /** @var Collection */
    protected $breakdownMap;

    /** @var BudgetRevision */
    protected $revision;

    public function __construct(BudgetRevision $revision)
    {
        $this->revision = $revision;
        $this->project = $revision->project;

        //If user is authorized use it in created by
        //Else it is generated by system
        $this->user = auth()->user();
        if (!$this->user) {
            $this->user = User::find(2);
        }
    }
    //</editor-fold>

    public function handle()
    {
        \Log::info("Creating a revision for project {$this->project->name}");
        $this->copyBasicModels();

        $this->copyBreakdownResources();

        $this->copyShadows();

        $this->sendNotificationEmail();
        \Log::info("Revision for project {$this->project->name} has been created");
    }

    //<editor-fold defaultstate=collapsed desc="Copy functions">
    protected function copyBasicModels()
    {
        \DB::insert("INSERT INTO revision_breakdowns(breakdown_id, revision_id, wbs_level_id, project_id, template_id, std_activity_id, cost_account, `code`, created_by, updated_by, created_at, updated_at)
                  SELECT id AS breakdown_id, {$this->revision->id} as revision_id, wbs_level_id, project_id, template_id, std_activity_id, cost_account, `code`, {$this->user->id} AS created_by, {$this->user->id} AS updated_by, now() AS created_at, now() AS updated_at FROM breakdowns WHERE project_id = {$this->project->id}");

        \DB::insert("INSERT INTO revision_boqs(boq_id, revision_id, wbs_id, item, description, type, unit_id, quantity, dry_ur, price_ur, arabic_description, division_id, `code`, item_code, cost_account, kcc_qty, subcon, materials, manpower, project_id, created_by, updated_by, created_at, updated_at) 
                  SELECT id AS boq_id, {$this->revision->id} as revision_id, wbs_id, item, description, type, unit_id, quantity, dry_ur, price_ur, arabic_description, division_id, `code`, item_code, cost_account, kcc_qty, subcon, materials, manpower, project_id, {$this->user->id} AS created_by, {$this->user->id} AS updated_by, now() AS created_at, now() AS updated_at FROM boqs WHERE project_id = {$this->project->id}");

        \DB::insert("INSERT INTO revision_qty_surveys(revision_id, qty_survey_id, cost_account, description, unit_id, budget_qty, eng_qty, deleted_at, wbs_level_id, project_id, `code`, discipline, created_by, updated_by, created_at, updated_at) 
                  SELECT {$this->revision->id} AS revision_id, id AS qty_survey_id, cost_account, description, unit_id, budget_qty, eng_qty, deleted_at, wbs_level_id, project_id, `code`, discipline, {$this->user->id} created_by, {$this->user->id} updated_by, now() AS created_at, now() AS updated_at FROM qty_surveys WHERE project_id = {$this->project->id}");

        \DB::insert("INSERT INTO revision_resources(original_id, revision_id, resource_type_id, resource_code, name, rate, unit, waste, reference, business_partner_id, project_id, resource_id, top_material, created_by, updated_by, created_at, updated_at)  
                  SELECT id AS original_id, {$this->revision->id} AS revision_id, resource_type_id, resource_code, name, rate, unit, waste, reference, business_partner_id, project_id, resource_id, top_material, {$this->user->id} AS created_by, {$this->user->id} AS updated_by, now() AS created_at, now() AS updated_at FROM resources WHERE project_id = {$this->project->id}");

        \DB::insert("INSERT INTO revision_productivities(original_id, revision_id, project_id, csi_code, csi_category_id, description, unit, crew_structure, crew_hours, crew_equip, daily_output, man_hours, equip_hours, reduction_factor, after_reduction, source, code, productivity_id, created_by, updated_by, created_at, updated_at)   
                  SELECT id AS original_id, {$this->revision->id} AS revision_id, project_id, csi_code, csi_category_id, description, unit, crew_structure, crew_hours, crew_equip, daily_output, man_hours, equip_hours, reduction_factor, after_reduction, source, code, productivity_id, {$this->user->id} AS created_by, {$this->user->id} AS updated_by, now() AS created_at, now() AS updated_at FROM productivities WHERE project_id = {$this->project->id}");
    }

    protected function copyBreakdownResources()
    {
        $this->breakdownMap = RevisionBreakdown::where('project_id', $this->project->id)->pluck('id', 'breakdown_id');
        $this->resourcesMap = RevisionResource::where('project_id', $this->project->id)->pluck('id', 'original_id');
        $this->productivityMap = RevisionProductivity::where('project_id', $this->project->id)->pluck('id', 'original_id');
        $this->boqMap = RevisionBoq::where('project_id', $this->project->id)->pluck('id', 'boq_id');
        $this->qtySurveyMap = RevisionQtySurvey::where('project_id', $this->project->id)->pluck('id', 'qty_survey_id');

        BreakdownResource::whereRaw('breakdown_id in (select id from breakdowns where project_id = ?)', [$this->project->id])->chunk(950, function (Collection $resources) {
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $newResources = $resources->map(function (BreakdownResource $r) use ($now) {
                $attributes = $r->getAttributes();
                $attributes['revision_id'] = $this->revision->id;
                $attributes['breakdown_resource_id'] = $attributes['id'];
                $attributes['breakdown_id'] = $this->breakdownMap->get($attributes['breakdown_id']);
                $attributes['resource_id'] = $this->resourcesMap->get($attributes['resource_id']);
                $attributes['productivity_id'] = $this->productivityMap->get($attributes['productivity_id']);
                $attributes['created_by'] = $attributes['updated_by'] = $this->user->id;
                $attributes['created_at'] = $attributes['updated_at'] = $now;
                unset($attributes['id']);
                return $attributes;
            });

            \DB::transaction(function () use ($newResources) {
                RevisionBreakdownResource::insert($newResources->toArray());
            });
        });
    }

    protected function copyShadows()
    {
        $this->breakdownResourceMap = RevisionBreakdownResource::whereRaw('breakdown_id in (select id from revision_breakdowns where project_id = ?)', [$this->project->id])->pluck('id', 'breakdown_resource_id');
        BreakDownResourceShadow::where('project_id', $this->project->id)->chunk(950, function (Collection $collection) {
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $new = $collection->map(function (BreakDownResourceShadow $r) use ($now) {
                $attributes = $r->getAttributes();
                $attributes['revision_id'] = $this->revision->id;
                $attributes['breakdown_id'] = $this->breakdownMap->get($attributes['breakdown_id']);
                $attributes['breakdown_resource_id'] = $this->breakdownResourceMap->get($attributes['breakdown_resource_id']);
                $attributes['resource_id'] = $this->resourcesMap->get($attributes['resource_id']);
                $attributes['productivity_id'] = $this->productivityMap->get($attributes['productivity_id']);
                $attributes['boq_id'] = $this->boqMap->get($attributes['boq_id']);
                $attributes['survey_id'] = $this->qtySurveyMap->get($attributes['survey_id']);
                $attributes['created_by'] = $attributes['updated_by'] = $this->user->id;
                $attributes['created_at'] = $attributes['updated_at'] = $now;
                unset($attributes['id']);
                return $attributes;
            });

            \DB::transaction(function () use ($new) {
                RevisionBreakdownResourceShadow::insert($new->toArray());
            });
        });
    }
    //</editor-fold>

    protected function sendNotificationEmail()
    {
        /** @var Collection $users */
        $users = $this->project->users->pluck('email');

        if ($this->project->cost_owner) {
            $users->prepend($this->project->cost_owner->email);
        }

        if ($this->project->owner) {
            $users->prepend($this->project->owner->email);
        }

        if (!$users->count()) {
            return false;
        }

        \Mail::send('mail.revision-created',
            ['project' => $this->project, 'revision' => $this->revision],
            function (Message $msg) use ($users) {
                $msg->to($users->toArray());
                $msg->subject("Revision {$this->revision->name} has been created for project {$this->project->name}");
            });
    }
}