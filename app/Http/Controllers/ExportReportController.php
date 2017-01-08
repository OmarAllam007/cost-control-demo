<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\Export\ExportProductivityReport;
use App\Http\Controllers\Reports\Export\ExportStandardActivityReport;
use App\Http\Controllers\Reports\Export\ExportWbsReport;
use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;

class ExportReportController extends Controller
{
    function exportWbsReport(Project $project){
        $exportWbs = new ExportWbsReport();
        $exportWbs->exportWbsReport($project);
    }

    function exportStdActivity(Project $project){

        $exportStdActivity = new ExportStandardActivityReport();
        $exportStdActivity->exportStandardActivityReport($project);
    }

    function exportProductivity(Project $project){

        $productivity = new ExportProductivityReport();
        $productivity->exportProductivityReport($project);
    }
}
