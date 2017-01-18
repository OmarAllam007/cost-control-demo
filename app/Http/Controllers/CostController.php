<?php

namespace App\Http\Controllers;

use App\CostShadow;
use App\Jobs\ImportOldDatasheet;
use App\Project;
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
            'remaining_qty' => 'required|numeric|gte:0', 'remaining_cost' => 'required|numeric|gte:0', 'remaining_unit_price' => 'required|numeric|gte:0',
            'progress' => 'numeric|gt:0|lte:100'
        ]);

        $cost_shadow->update($request->all());
        $cost_shadow->budget->update($request->get('budget'));

        flash('Resource data has been updated', 'success');
        return \Redirect::to('/blank?reload=breakdowns');
    }

    function importOldData(Project $project)
    {
        if (cannot('cost_owner', $project)) {
            flash('You are not authorized to do this action');
            return \Redirect::route('project.cost-control', $project);
        }

        return view('project.import_old_cost');
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

        flash($result['success'] . ' records has been imported');
        return \Redirect::route('project.cost-control', $project);
    }
}
