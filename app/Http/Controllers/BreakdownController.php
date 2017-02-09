<?php

namespace App\Http\Controllers;


use App\Breakdown;
use App\Http\Controllers\Reports\ActivityResourceBreakDown;
use App\Http\Controllers\Reports\BoqPriceList;
use App\Http\Controllers\Reports\BudgetCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostDryCostByDiscipline;
use App\Http\Controllers\Reports\HighPriorityMaterials;
use App\Http\Controllers\Reports\Productivity;
use App\Http\Controllers\Reports\QtyAndCost;
use App\Http\Controllers\Reports\QuantitiySurveySummery;
use App\Http\Controllers\Reports\ResourceDictionary;
use App\Http\Controllers\Reports\RevisedBoq;
use App\Http\Requests\BreakdownRequest;
use App\Jobs\Export\ExportBreakdownJob;
use App\Jobs\PrintReport\PrintAllJob;
use App\Project;
use App\Resources;
use App\Survey;
use App\WbsLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use phpDocumentor\Reflection\Types\Null_;

class BreakdownController extends Controller
{
    public function create(Request $request)
    {

        if (!$request->has('project')) {
            return \Redirect::route('project.index');
        }
        $project = Project::find($request->get('project'));
        if (!$project) {
            flash('Project not found');
            return \Redirect::route('project.index');
        }
        return view('breakdown.create');
    }

    /**
     * @param WbsLevel $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BreakdownRequest $request)
    {

        $breakdown = Breakdown::create($request->all());
        $breakdown->syncVariables($request->get('variables'));
        $resources = $breakdown->resources()->createMany($request->get('resources'));

        return \Redirect::to('/blank?reload=breakdown');
    }

    public function duplicate(Breakdown $breakdown)
    {
        return view('breakdown.duplicate', compact('breakdown'));
    }

    public function postDuplicate(Request $request, Breakdown $breakdown)
    {
        $this->validate($request, ['wbs_level_id' => 'required', 'cost_account' => 'required']);

        $duplicate = $breakdown->duplicate($request->only('wbs_level_id', 'cost_account'));

        flash('Breakdown has been duplicated', 'success');
        return \Redirect::to('/blank?reload=breakdown');
    }

    public function edit(Breakdown $breakdown)
    {
        return view('breakdown.edit', compact('breakdown'));
    }

    public function update(Request $request, Breakdown $breakdown)
    {

    }

    public function delete(Breakdown $breakdown)
    {

    }

    function filters(Request $request, Project $project)
    {
        $data = $request->except('_token');
        \Session::set('filters.breakdown.' . $project->id, $data);

        return \Redirect::to(route('project.show', $project) . '#breakdown');
    }

    function exportBreakdown(Project $project)
    {
        $file = $this->dispatch(new ExportBreakdownJob($project));
        $response = \Response::download($file, slug($project->name) . '-breakdown.csv', ['ContentType' => 'text/csv']);
        return $response;
    }

    function printAll(Project $project)
    {
        $this->dispatch(new PrintAllJob($project));
    }


}