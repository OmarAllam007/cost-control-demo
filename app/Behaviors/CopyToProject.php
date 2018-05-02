<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/13/17
 * Time: 2:42 PM
 */

namespace App\Behaviors;

use App\BreakdownTemplate;
use App\BreakdownVariable;
use App\Productivity;
use App\Resources;
use App\WbsLevel;
use App\Breakdown;
use App\BreakdownResource;
use App\StdActivityResource;
use Make\Makers\Resource;

trait CopyToProject
{
    protected $template_map;

    function copyToProject($project_id, $parent_id = 0, $template_map = null)
    {
        if ($template_map) {
            $this->template_map = $template_map;
        } else {
            $this->template_map = collect();
        }

        $user_id = auth()->id() ?: 2;
        $attributes = $this->getAttributes();
        unset($attributes['id']);
        $attributes['project_id'] = $project_id;
        $attributes['parent_id'] = $parent_id;
        $attributes['created_at'] = $attributes['updated_at'] = date('Y-m-d H:i:s');
        $attributes['created_by'] = $attributes['updated_by'] = $user_id;

        $new_wbs_id = \DB::table('wbs_levels')->insertGetId($attributes);

        \DB::insert("insert into boqs(wbs_id, item, description, type, unit_id, quantity, dry_ur, price_ur, arabic_description, created_at, updated_at, division_id, code, item_code, cost_account, kcc_qty, subcon, materials, manpower, project_id, created_by, updated_by) 
  select $new_wbs_id as wbs_id, item, description, type, unit_id, quantity, dry_ur, price_ur, arabic_description, now() as created_at, now() as updated_at, division_id, code, item_code, cost_account, kcc_qty, subcon, materials, manpower, $project_id as project_id, $user_id, $user_id
  from boqs where wbs_id = {$this->id}");

        \DB::insert("insert into qty_surveys(cost_account, item_code, description, unit_id, budget_qty, eng_qty, deleted_at, created_at, updated_at, wbs_level_id, project_id, qs_code, discipline, created_by, updated_by)
    select cost_account, item_code, description, unit_id, budget_qty, eng_qty, deleted_at, now() as created_at, now() as updated_at, $new_wbs_id as wbs_level_id, $project_id as project_id, qs_code, discipline, $user_id as created_by, $user_id as updated_by
    from qty_surveys
    where wbs_level_id = {$this->id}");

        $this->breakdowns()
            ->with('template', 'template.resources', 'resources', 'resources.resource', 'resources.productivity')
            ->get()->each(function ($breakdown) use ($project_id, $new_wbs_id) {
                $this->copyBreakdown($breakdown, $project_id, $new_wbs_id);
            });

        $this->children->each(function (WbsLevel $level) use ($project_id, $new_wbs_id, $template_map) {
            $level->copyToProject($project_id, $new_wbs_id, $template_map);
        });
    }

    private function copyBreakdown($breakdown, $project_id, $wbs_id)
    {
        $user_id = auth()->id() ?: 2;
        list($new_template_id, $tpl_resource_mapping) = $this->copyTemplate($breakdown->template, $project_id);

        $attributes = $breakdown->getAttributes();
        unset($attributes['id']);
        $attributes['project_id'] = $project_id;
        $attributes['wbs_level_id'] = $wbs_id;
        $attributes['template_id'] = $new_template_id;
        $attributes['created_at'] = date('Y-m-d H:i:s');
        $attributes['updated_at'] = date('Y-m-d H:i:s');
        $attributes['created_by'] = $user_id;
        $attributes['updated_by'] = $user_id;

        $new_breakdown_id = Breakdown::insertGetId($attributes);
        $new_breakdown = Breakdown::find($new_breakdown_id);

        foreach ($breakdown->variables as $variable) {
            $varAttributes = $variable->getAttributes();
            unset($varAttributes['id']);
            $varAttributes['breakdown_id'] = $new_breakdown_id;
            $varAttributes['qty_survey_id'] = $new_breakdown->qty_survey->id ?? 0;
            $varAttributes['created_at'] = date('Y-m-d H:i:s');
            $varAttributes['updated_at'] = date('Y-m-d H:i:s');
            $varAttributes['created_by'] = $user_id;
            $varAttributes['updated_by'] = $user_id;

            BreakdownVariable::insert($varAttributes);
        }

        BreakdownResource::unguard();
        $breakdown_resources = $breakdown->resources()
            ->whereRaw("id in (select breakdown_resource_id from break_down_resource_shadows where breakdown_id = {$breakdown->id})")
            ->get();

        foreach ($breakdown_resources as $resource) {
            $attributes = $resource->getAttributes();
            unset($attributes['id']);
            $attributes['project_id'] = $project_id;
            $attributes['breakdown_id'] = $new_breakdown_id;
            $attributes['std_activity_resource_id'] = $tpl_resource_mapping->get($resource->std_activity_resource_id, 0);
            $attributes['resource_id'] = $resource->resource->resource_id ?: $resource->resource->id;
            $attributes['productivity_id'] = $resource->productivity->productivity_id ?? 0;
            $attributes['created_at'] = date('Y-m-d H:i:s');
            $attributes['updated_at'] = date('Y-m-d H:i:s');
            $attributes['created_by'] = $user_id;
            $attributes['updated_by'] = $user_id;

            $resource_id = $attributes['resource_id'];
            $targetResource = Resources::where('project_id', $project_id)->where('resource_id', $resource_id)->first();
            if (!$targetResource) {
                $resourceAttributes = $resource->resource->getAttributes();
                unset($resourceAttributes['id']);
                $resourceAttributes['project_id'] = $project_id;
                $resourceAttributes['created_at'] = date('Y-m-d H:i:s');
                $resourceAttributes['updated_at'] = date('Y-m-d H:i:s');
                $resourceAttributes['created_by'] = $user_id;
                $resourceAttributes['updated_by'] = $user_id;

                Resources::insertGetId($resourceAttributes);
            }

            if ($attributes['productivity_id']) {
                $productivity_id = $attributes['productivity_id'];
                $targetProductivity = Productivity::where('project_id', $project_id)->where('productivity_id', $productivity_id)->first();
                if (!$targetProductivity) {
                    $productivityAttributes = $resource->productivity->getAttributes();
                    unset($productivityAttributes['id']);
                    $productivityAttributes['project_id'] = $project_id;
                    $productivityAttributes['created_at'] = date('Y-m-d H:i:s');
                    $productivityAttributes['updated_at'] = date('Y-m-d H:i:s');
                    $productivityAttributes['created_by'] = $user_id;
                    $productivityAttributes['updated_by'] = $user_id;

                    Productivity::insertGetId($productivityAttributes);
                }
            }

            BreakdownResource::create($attributes);
        }

    }

    private function copyTemplate($template, $project_id)
    {
        $user_id = auth()->id() ?: 2;

        if ($this->template_map->has($template->id)) {
            return $this->template_map->get($template->id);
        }

        $attributes = $template->getAttributes();
        unset($attributes['id']);
        $attributes['created_at'] = date('Y-m-d H:i:s');
        $attributes['updated_at'] = date('Y-m-d H:i:s');
        $attributes['created_by'] = $user_id;
        $attributes['updated_by'] = $user_id;
        $attributes['project_id'] = $project_id;

        $new_template_id = \DB::table('breakdown_templates')->insertGetId($attributes);

        $old_resources = StdActivityResource::where('template_id', $template->id)->get();

        $tpl_resource_mapping = collect();

        $old_resources->each(function ($resource) use ($tpl_resource_mapping, $user_id, $project_id, $new_template_id) {
            $attributes = $resource->getAttributes();
            unset($attributes['id']);
            $attributes['created_at'] = date('Y-m-d H:i:s');
            $attributes['updated_at'] = date('Y-m-d H:i:s');
            $attributes['created_by'] = $user_id;
            $attributes['updated_by'] = $user_id;
            $attributes['project_id'] = $project_id;
            $attributes['template_id'] = $new_template_id;

            $new_resource_id = StdActivityResource::insertGetId($attributes);

            $tpl_resource_mapping->put($resource->id, $new_resource_id);
        });

        $this->template_map->put($template->id, $result = [$new_template_id, $tpl_resource_mapping]);

        return $result;
    }


}