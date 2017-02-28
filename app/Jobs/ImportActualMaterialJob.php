<?php

namespace App\Jobs;

use App\ActivityMap;
use App\ActualBatch;
use App\ActualResources;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Period;
use App\Project;
use App\ResourceCode;
use App\Resources;
use App\Support\CostImporter;
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
        $loader->getReadDataOnly();
        $excel = $loader->load($this->file);

        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);


        $material = collect();
        $batch = ActualBatch::create(['type' => 'material', 'user_id' => \Auth::id(), 'file' => $this->file, 'project_id' => $this->project->id, 'period_id' => $this->project->open_period()->id]);

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Worksheet_CellIterator */
            $cells->setIterateOnlyExistingCells(true);
            $data = $this->getDataFromCells($cells);

            // Row is empty, skip
            if (!array_filter(array_map('trim', $data))) {
                continue;
            }

            $hash = str_random(8);

            $dateVal = $sheet->getCell('B' . $row->getRowIndex())->getValue();
            $data[1] = Carbon::create(1899, 12, 30)->addDays($dateVal)->format('Y-m-d');

            $material->put($hash, $data);
        }

        $costImporter = new CostImporter($batch, $material);
        return $costImporter->checkMapping();

//        return dispatch(new ImportMaterialDataJob($this->project, $material, $batch));
    }
}
