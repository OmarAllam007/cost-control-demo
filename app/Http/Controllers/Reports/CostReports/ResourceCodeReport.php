<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 26/12/16
 * Time: 11:51 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\Project;

class ResourceCodeReport
{
    public function getResourceCodeReport(Project $project)
    {
        dd($project);
    }

}