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

class SemiActivityRollupController extends Controller
{
    function create(Project $project)
    {
        $this->authorize('cost_owner', $project);

        return view('rollup.semi-activity', compact('project'));
    }

    function store(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $extra = $request->only(['budget_unit', 'measure_unit', 'to_date_qty', 'progress', ]);
        $rollup = new SemiActivityRollup($project, $request->get('resources', []), $extra);

        $status = $rollup->handle();

        flash("{$status['resources']} Resources in {$status['activities']} activities have been rolled up", 'success');

        return \Redirect::route('project.cost-control', $project);
    }
}