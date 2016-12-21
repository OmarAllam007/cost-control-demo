<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reports\CostReports\ProjectInformation;
use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;

class CostReportsController extends Controller
{

    public function projectInformation(Project $project)
    {
        $projectInfo = new ProjectInformation();
        return $projectInfo->getProjectInformation($project);
    }
}