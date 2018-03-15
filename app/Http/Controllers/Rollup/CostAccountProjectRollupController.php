<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 08/03/2018
 * Time: 8:28 AM
 */

namespace App\Http\Controllers\Rollup;


use App\Http\Controllers\Controller;
use App\Project;
use App\Rollup\Actions\CostAccountRollup;

class CostAccountProjectRollupController extends Controller
{
    function store(Project $project)
    {
        $this->authorize('cost_owner', $project);

        $project->breakdowns()
            ->select('id')->chunk(100, function ($breakdowns) use ($project) {
                \DB::beginTransaction();
                $rollup = new CostAccountRollup($project, $breakdowns->pluck('id')->toArray());
                $rollup->handle();
                \DB::commit();
            });


        flash("Project has been rolled up on cost account level", 'success');
        return redirect()->route('project.cost-control', $project);
    }
}