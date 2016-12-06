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
use App\Survey;
use App\WbsLevel;
use Illuminate\Http\Request;

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

    public function store(BreakdownRequest $request)
    {
//        dump($request->all());
//        dd($request->all());
        $wbs_level = WbsLevel::find($request->wbs_level_id);
        $survey_level = Survey::where('wbs_level_id',$wbs_level->id)->where('cost_Account',$request->cost_account)->first();
        $eng_qty = 0;
        if($survey_level){
            $eng_qty = $survey_level->eng_qty;
        }
        else{
            $parent = $wbs_level;
            while($parent->parent){
                $parent_wbs_level = WbsLevel::find($parent->id);
                $parent_survey = Survey::where('wbs_level_id',$parent_wbs_level->id)->where('cost_Account',$request->cost_account)->first();
                if($parent_survey){
                    $eng_qty = $parent_survey->eng_qty;
                    break;
                }
                $parent = $parent->parent;
            }
        }


        $breakdown = Breakdown::create($request->all());
        $resource_code = $breakdown->wbs_level->code . $breakdown->std_activity->id_partial;

        $resources = $breakdown->resources()->createMany($request->get('resources'));
        foreach ($resources as $resource) {
            $resource->update(['code' => $resource_code,'eng_qty'=>$eng_qty]);

        }
        $breakdown->syncVariables($request->get('variables'));
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
    function exportBreakdown(Project $project){

        $this->dispatch(new ExportBreakdownJob($project));
    }

    function printAll(Project $project)
    {
        $this->dispatch(new PrintAllJob($project));
    }


}