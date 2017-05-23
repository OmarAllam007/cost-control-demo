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

        $this->validate($request, [
            'remaining_qty' => 'required|numeric|gte:0', 'remaining_unit_price' => 'required|numeric|gte:0',
            'allowable_ev_cost' => 'required|numeric|gte:0',
            'progress' => 'numeric|gt:0|lte:100'
        ]);

        CostShadow::flushEventListeners();
        BreakDownResourceShadow::flushEventListeners();

        $fields = ['remaining_qty', 'remaining_unit_price'];
        if ($cost_shadow->budget->activity->isGeneral()) {
            $fields[] = 'allowable_ev_cost';
        }
        $cost_shadow->fill($request->only('fields'));
        if ($cost_shadow->isDirty()) {
            $cost_shadow->manual_edit = true;
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
