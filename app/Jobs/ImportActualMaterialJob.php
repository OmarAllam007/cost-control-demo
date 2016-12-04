<?php

namespace App\Jobs;

use App\ActivityMap;
use App\ActualResources;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Period;
use App\Project;
use App\ResourceCode;
use App\Resources;
use App\Unit;
use App\UnitAlias;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ImportActualMaterialJob extends ImportJob
{
    protected $file;

    /** @var Project */
    protected $project;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;

    }

    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);

        $material = collect();

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);

            // Row is empty, skip
            if (!array_filter($data)) {
                continue;
            }

            $material->push($data);
        }

        return dispatch(new ImportMaterialDataJob($this->project, $material));
    }
}
