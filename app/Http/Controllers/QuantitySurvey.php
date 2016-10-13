<?php
namespace App\Http\Controllers\Exports;

use App\Project;

class QuantitySurvey
{
    public function exportQS(Project $project)
    {
        $excel_file =new \PHPExcel();
        foreach ($project->quantities() as $quantity){

        }

    }

}