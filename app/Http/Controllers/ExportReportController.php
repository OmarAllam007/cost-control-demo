<?php

namespace App\Http\Controllers;

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
}
