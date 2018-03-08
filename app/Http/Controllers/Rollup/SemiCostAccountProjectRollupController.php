<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 08/03/2018
 * Time: 8:38 AM
 */

namespace App\Http\Controllers\Rollup;


use App\Http\Controllers\Controller;
use App\Project;
use App\Rollup\Actions\SemiCostAccountRollup;
use Illuminate\Http\Request;

class SemiCostAccountProjectRollupController extends Controller
{
    function store(Project $project, Request $request)
    {
        $this->authorize('cost_owner', $project);

        $data = $project->breakdowns()->has('rollable_resources')
            ->with('rollable_resources')->get()->keyBy('id')->map(function ($breakdown) {
                return $breakdown->rollable_resources->pluck('id', 'id');
            });

        $rollup = new SemiCostAccountRollup($project, $data);
        $rollup->handle();


        flash("Project has been rolled up on cost account level except important resources", 'success');
        return redirect()->route('project.cost-control', $project);
    }
}