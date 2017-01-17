<?php

namespace App\Jobs;

use App\ActualBatch;
use App\CostShadow;
use App\Jobs\Job;
use App\Project;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportOldDatasheet extends ImportJob // implements ShouldQueue
{
    //use InteractsWithQueue, SerializesModels;

    /**
     * @var Project
     */
    protected $project;

    protected $file;

    /**
     * @var ActualBatch
     */
    protected $batch;

    protected $period_id;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->period_id = $project->open_period()->id;
        $this->batch = ActualBatch::create([
            'user_id' => \Auth::id(), 'type' => 'Old Data', 'file' => $file, 'project_id' => $project->id,
            'period_id' => $this->period_id
        ]);
    }

    public function handle()
    {
        set_time_limit(300);
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);

        $success = 0;
        foreach ($rows as $row) {
            $rowData = $this->getDataFromCells($row->getCellIterator());
            if (!array_filter($rowData)) {
                continue;
            }

            $entry = $this->getEntry($rowData);
            if ($entry) {
                $success++;
            }
        }

        return $success;
    }

    protected function getEntry($rowData) : CostShadow
    {
        return CostShadow::create([

        ]);
    }
}
