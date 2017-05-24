<?php

namespace App\Http\Controllers;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostShadow;
use App\Jobs\ImportOldDatasheet;
use App\Project;
use App\Support\CostShadowCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CostController extends Controller
{
    public function show(CostShadow $cost_shadow)
    {

    }

    public function pseudoEdit(BreakdownResource $breakdown_resource) {
        $shadow = BreakDownResourceShadow::whereBreakdownResourceId($breakdown_resource->id)->first();

        $current_period_id = $shadow->project->open_period()->id;

        $costShadow = CostShadow::whereBreakdownResourceId($breakdown_resource->id)
            ->where('period_id', '<=', $current_period_id)
            ->orderBy('period_id', 'DESC')->first();

        if (!$costShadow) {
            $attributes = [
                'project_id' => $shadow->project_id,
                'wbs_level_id' => $shadow->wbs_id,
                'period_id' => $current_period_id,
                'breakdown_resource_id' => $breakdown_resource->id
            ];

            $costShadow = CostShadow::create($attributes);
        } elseif ($costShadow->period_id != $current_period_id) {
            $attributes = $costShadow->getAttributes();
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
            unset($attributes['period_id']);

            $attributes['manual_edit'] = 0;
            $attributes['period_id'] = $current_period_id;
            $attributes['curr_qty'] = 0;
            $attributes['curr_cost'] = 0;
            $attributes['curr_unit_price'] = 0;
            $attributes['prev_qty'] = $attributes['to_date_qty'];
            $attributes['prev_cost'] = $attributes['to_date_cost'];
            $attributes['prev_unit_price'] = $attributes['to_date_unit_price'];
            $attributes['period_id'] = $current_period_id;

            $costShadow = CostShadow::create($attributes);
        }

        return redirect()->to(route('cost.edit', $costShadow) . (request()->iframe? '?iframe=1' : ''));
    }

    public function edit(CostShadow $cost_shadow)
    {
        return view('cost.edit', compact('cost_shadow'));
    }

    public function update(Request $request, CostShadow $cost_shadow)
    {
        if (cannot('manual_edit', $cost_shadow->project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.index');
        }

        $rules = [
            'remaining_qty' => 'required|numeric|gte:0', 'remaining_unit_price' => 'required|numeric|gte:0',
            'progress' => 'numeric|gt:0|lte:100'
        ];

        if ($cost_shadow->budget->std_activity->isGeneral()) {
            $rules['allowable_ev_cost'] = 'required|numeric|gte:0';
        }

        $this->validate($request, $rules);

        CostShadow::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();

        $fields = ['remaining_qty', 'remaining_unit_price'];
        if ($cost_shadow->budget->std_activity->isGeneral()) {
            $fields[] = 'allowable_ev_cost';
        }
        $cost_shadow->fill($request->only($fields));
        if ($cost_shadow->isDirty()) {
            $cost_shadow->manual_edit = 1;
            $cost_shadow->save();
        }

        $budget = $request->get('budget', ['progress' => 0, 'status' => '']);
        if (strtolower($budget['status']) == 'closed') {
            $budget['progress'] = 100;
        } elseif ($budget['progress'] == 100) {
            $budget['status'] = 'Closed';
        }
        $cost_shadow->budget->update(['progress' => $budget['progress'], 'status' => $budget['status']]);

        $cost_shadow->recalculate(true);

        flash('Resource data has been updated', 'success');
        return \Redirect::to('/blank?reload=breakdowns');
    }

    function importOldData(Project $project)
    {
        if (cannot('cost_owner', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        return view('project.cost-control.import_old_cost', compact('project'));
    }

    function postImportOldData(Project $project, Request $request)
    {
        if (cannot('cost_owner', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        $this->validate($request, ['file' => 'required|file|mimes:xls,xlsx']);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $filename = $file->move(storage_path('batches'), uniqid() . '.' . $file->clientExtension());

        $result = $this->dispatch(new ImportOldDatasheet($project, $filename));

        flash($result['success'] . ' records have been imported', 'info');
        return \Redirect::route('project.cost-control', $project);
    }
}
