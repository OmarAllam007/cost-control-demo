<?php

namespace App\Http\Controllers;

use App\BreakDownResourceShadow;
use App\Project;
use App\ResourceType;
use App\StdActivity;
use App\Support\Rollup;
use App\WbsLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RollupController extends Controller
{
    function create(Project $project, WbsLevel $wbsLevel, StdActivity $stdActivity, Request $request)
    {
        if (cannot('actual_resources', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $resources = BreakDownResourceShadow::whereIn('breakdown_resource_id', $request->get('resource', [0]))->get();

        $status = $this->validateResources($resources, $project, $wbsLevel, $stdActivity);
        if (!$status['ok']) {
            flash($status['message']);
            return \Redirect::route('project.cost-control', $project);
        }

        $key = uniqid('', true);
        $data = compact('project', 'wbsLevel', 'stdActivity', 'resources', 'key');

        $budget_cost = $resources->sum('budget_cost');
        $actual_cost = $resources->sum('to_date_cost');
        $progress = 0;
        if ($budget_cost) {
            $progress = round($actual_cost * 100/$budget_cost, 2);
        }
        $data['progress'] = $progress;
        \Cache::put($key, $data, Carbon::tomorrow());

        $data['code'] = $resources->first()->code . '.01';
        $data['name'] = $stdActivity->name . ' rollup';
        $data['type'] = '';
        $data['resourceTypes'] = ResourceType::where('parent_id', 0)->pluck('name', 'id');

        return view('rollup.create', $data);
    }

    function store($key, Request $request)
    {
        $data = \Cache::get($key);
        if (!$data) {
            flash('No data found');
            if ($request->exists('iframe')) {
                return \Redirect::to('/blank');
            }
            return \Redirect::route('projects.index');
        }

        $project = $data['project'];
        if (cannot('actual_resources', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $this->validate($request, [
            'code' => 'required', 'name' => 'required', 'type' => 'required',
            'qty' => 'required|numeric|gte:0', 'progress' => 'required|numeric|gte:0'
        ]);

        $rollup = new Rollup($key, $request->only('code', 'name', 'qty', 'type', 'progress'));
        $rollup->handle();

        flash('Resources has been rolled up', 'success');
        if ($request->exists('iframe')) {
            return \Redirect::to('/blank?reload=breakdowns');
        }
        return \Redirect::route('project.cost-control', $project);
    }

    private function validateResources(Collection $resources, $project, $wbsLevel, $stdActivity)
    {
        $projects = $resources->pluck('project_id')->unique();
        if ($projects->count() > 1) {
            return ['ok' => false, 'message' => 'Resources are not in the same project'];
        }

        if ($projects->first() != $project->id) {
            return ['ok' => false, 'message' => 'Resources are not in the selected project'];
        }

        $wbs = $resources->pluck('wbs_id')->unique();
        if ($wbs->count() > 1) {
            return ['ok' => false, 'message' => 'Resources are not in the same WBS'];
        }

        if ($wbs->first() != $wbsLevel->id) {
            return ['ok' => false, 'message' => 'Resources are not in the selected WBS'];
        }


        $activities = $resources->pluck('activity_id')->unique();
        if ($activities->count() > 1) {
            return ['ok' => false, 'message' => 'Resources are not in the same activity'];
        }

        if ($activities->first() != $stdActivity->id) {
            return ['ok' => false, 'message' => 'Resources are not in the selected activity'];
        }

        return ['ok' => true];
    }
}
