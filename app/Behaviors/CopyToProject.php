<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 11/13/17
 * Time: 2:42 PM
 */

namespace App\Behaviors;

use App\WbsLevel;
use App\Breakdown;
use App\BreakdownResource;
use App\StdActivityResource;

trait CopyToProject
{
    function copyToProject($project_id, $parent_id = 0)
    {
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

        //TODO: add item code here
        \DB::insert("insert into qty_surveys(cost_account, description, unit_id, budget_qty, eng_qty, deleted_at, created_at, updated_at, wbs_level_id, project_id, code, discipline, created_by, updated_by)
    select cost_account, description, unit_id, budget_qty, eng_qty, deleted_at, now() as created_at, now() as updated_at, $new_wbs_id as wbs_level_id, $project_id as project_id, code, discipline, $user_id as created_by, $user_id as updated_by
    from qty_surveys
    where wbs_level_id = {$this->id}");

        \DB::beginTransaction();

        $this->breakdowns()
            ->with('template', 'template.resources', 'resources', 'resources.resource', 'resources.productivity')
            ->get()->each(function ($breakdown) use ($project_id, $new_wbs_id) {
                $this->copyBreakdown($breakdown, $project_id, $new_wbs_id);
            });

        \DB::commit();


        $this->children->each(function (WbsLevel $level) use ($project_id, $new_wbs_id) {
            $level->copyToProject($project_id, $new_wbs_id);
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
        BreakdownResource::unguard();
        foreach ($breakdown->resources as $resource) {
            $attributes = $resource->getAttributes();
            unset($attributes['id']);
            $attributes['breakdown_id'] = $new_breakdown_id;
            $attributes['std_activity_resource_id'] = $tpl_resource_mapping->get($resource->std_activity_resource_id);
            $attributes['resource_id'] = $resource->resource->resource_id;
            $attributes['productivity_id'] = $resource->productivity->productivity_id ?? 0;
            $attributes['created_at'] = date('Y-m-d H:i:s');
            $attributes['updated_at'] = date('Y-m-d H:i:s');
            $attributes['created_by'] = $user_id;
            $attributes['updated_by'] = $user_id;

            BreakdownResource::create($attributes);
        }

    }

    private function copyTemplate($template, $project_id)
    {
        $target = \DB::table('breakdown_templates')
            ->where('project_id', $project_id)
            ->where('parent_template_id', $template['template_id'])
            ->first();

        $user_id = auth()->id() ?: 2;

        if ($target) {
            $new_template_id = $target->id;
        } else {
            $attributes = $template->getAttributes();
            unset($attributes['id']);
            $attributes['created_at'] = date('Y-m-d H:i:s');
            $attributes['updated_at'] = date('Y-m-d H:i:s');
            $attributes['created_by'] = $user_id;
            $attributes['updated_by'] = $user_id;
            $attributes['project_id'] = $project_id;

            $new_template_id = \DB::table('breakdown_templates')->insertGetId($attributes);
        }

        $old_resources = StdActivityResource::where('template_id', $template->id)
            ->get()->keyBy(function ($resource) {
                return $resource->resource_id . '.' . $resource->remarks;
            });

        $new_resources = StdActivityResource::where('template_id', $new_template_id)
            ->get()->keyBy(function ($resource) {
                return $resource->resource_id . '.' . $resource->remarks;
            });

        $tpl_resource_mapping = collect();

        $old_resources->each(function ($resource) use ($new_resources, $tpl_resource_mapping, $user_id, $project_id, $new_template_id) {
            $hash = $resource->resource_id . '.' . $resource->remarks;
            if ($new_resources->has($hash)) {
                $new_resource_id = $new_resources->get($hash)->id;
            } else {
                $attributes = $resource->getAttributes();
                unset($attributes['id']);
                $attributes['created_at'] = date('Y-m-d H:i:s');
                $attributes['updated_at'] = date('Y-m-d H:i:s');
                $attributes['created_by'] = $user_id;
                $attributes['updated_by'] = $user_id;
                $attributes['project_id'] = $project_id;
                $attributes['template_id'] = $new_template_id;
                $new_resource_id = StdActivityResource::insertGetId($attributes);
            }

            $tpl_resource_mapping->put($resource->id, $new_resource_id);
        });

        return [$new_template_id, $tpl_resource_mapping];
    }


}