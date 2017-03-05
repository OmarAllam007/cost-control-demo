<?php

namespace App\Http\Controllers;

use App\ActivityMap;
use App\BreakDownResourceShadow;
use App\Jobs\Export\ExportActivityMapping;
use App\Jobs\ImportActivityMapsJob;
use App\Project;
use Illuminate\Http\Request;

class ActivityMapController extends Controller
{
    function import(Project $project)
    {
        if (cannot('activity_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        return view('activity-map.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        if (cannot('activity_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $file = $request->file('file');

        $result = $this->dispatch(new ImportActivityMapsJob($project, $file->path()));

        if ($result['failed']->count()) {
            $key = 'activity_map_' . time();
            \Cache::put($key, $result, 180);
            flash("Could not import some activities", 'warning');
            return \Redirect::route('activity-map.fix-import', compact('project', 'key'));
        }


        flash($result['success'] . ' Records have been imported', 'success');
        return \Redirect::route('project.cost-control', $project);
    }

    function fixImport(Project $project, $key)
    {
        if (cannot('activity_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        if (!\Cache::has($key)) {
            flash('No data found', 'warning');
            return \Redirect::route('project.cost-control', $project);
        }

        $result = \Cache::get($key);
        $rows = $result['failed'];

        $codes = BreakDownResourceShadow::where('project_id', $project->id)->selectRaw('DISTINCT code, wbs_id, activity')
            ->get();

        return view('activity-map.fix-import', compact('rows', 'codes', 'project'));
    }

    function postFixImport(Project $project, $key)
    {
        if (cannot('activity_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        if (!\Cache::has($key)) {
            flash('No data found', 'warning');
            return \Redirect::route('project.cost-control', $project);
        }

        $result = \Cache::get($key);

        $data = request('mapping');
        foreach ($data as $key => $value) {
            if ($value) {
                ActivityMap::updateOrCreate([
                    'project_id' => $project->id, 'activity_code' => $value, 'equiv_code' => $key
                ]);

                ++$result['success'];
            }
        }

        flash($result['success'] . ' Records have been imported', 'success');
        return \Redirect::route('project.cost-control', $project);
    }

    function delete(Project $project)
    {
        if (cannot('activity_mapping', $project)) {
            flash("You are not authorized to do this action");
            return \Redirect::route('project.cost-control', $project);
        }

        ActivityMap::where('project_id', $project->id)->delete();

        flash('All activity mapping for this project has been deleted', 'info');
        return \Redirect::route('activity-map.import', $project);
    }

    function exportActivityMapping(Project $project){
        $this->dispatch(new ExportActivityMapping($project));
    }
}
