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
use App\StoreResource;
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

    /** @var Period */
    private $period;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->period = $this->project->open_period();
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
        $batch = ActualBatch::create(['type' => 'material', 'user_id' => \Auth::id(), 'file' => $this->file, 'project_id' => $this->project->id, 'period_id' => $this->period->id]);

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Worksheet_CellIterator */
            $cells->setIterateOnlyExistingCells(true);
            $data = $this->getDataFromCells($cells);

            // Row is empty, skip
            if (!array_filter(array_map('trim', $data))) {
                continue;
            }

            $dateVal = $sheet->getCell('B' . $row->getRowIndex())->getValue();
            $data[1] = Carbon::create(1899, 12, 30)->addDays($dateVal)->format('Y-m-d');

            $row_id = StoreResource::create([
                'project_id' => $this->project->id, 'period_id' => $this->period->id, 'batch_id' => $batch->id,
                'activity_code' => $data[0], 'store_date' => $data[1], 'item_desc' => $data[2],
                'measure_unit' => $data[3], 'qty' => $data[4], 'unit_price' => $data[5], 'cost' => $data[6],
                'item_code' => $data[7], 'doc_no' => $data[8],
            ]);

            $material->put($row_id, $data);
        }

        $costImporter = new CostImporter($batch, $material);
        return $costImporter->checkMapping();
    }
}
