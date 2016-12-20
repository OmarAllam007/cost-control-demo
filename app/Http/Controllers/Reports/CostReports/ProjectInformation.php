<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 20/12/16
 * Time: 03:45 م
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\Project;

class ProjectInformation
{
    function getProjectInformation(Project $project)
    {

        return view('reports.cost-control.project_information');
        
    }
}