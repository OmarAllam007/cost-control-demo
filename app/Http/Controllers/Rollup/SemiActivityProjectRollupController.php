<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 08/03/2018
 * Time: 8:51 AM
 */

namespace App\Http\Controllers\Rollup;


use App\Http\Controllers\Controller;
use App\Project;
use App\Rollup\Actions\SemiActivityRollup;
use Illuminate\Http\Request;

class SemiActivityProjectRollupController extends Controller
{
    function store(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $resources = $project->shadows()->selectRaw('code, breakdown_resource_id')
            ->where('important', 0)->whereNull('rolled_up_at')->where('is_rollup', 0)
            ->get()->groupBy('code')->map(function($group) {
                return $group->pluck('breakdown_resource_id', 'breakdown_resource_id');
            })->groupBy('code');

        $rollup = new SemiActivityRollup($project, $resources);

        $status = $rollup->handle();

        flash("{$status['resources']} Resources in {$status['activities']} activities have been rolled up", 'success');

        return \Redirect::route('project.cost-control', $project);
    }
}