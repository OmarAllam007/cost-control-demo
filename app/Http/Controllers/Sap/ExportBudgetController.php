<?php

namespace App\Http\Controllers\Sap;

use App\Project;
use App\Http\Controllers\Controller;

class ExportBudgetController extends Controller
{
    function show(Project $project)
    {
        $this->authorize('budget', $project);

        $exporter = new \App\Sap\ExportBudget($project);
        $result = $exporter->handle();

        $export_name = slug($project->name. '-sap_export.xlsx');
        return response()->download($result)->deleteFileAfterSend(true);
    }
}
