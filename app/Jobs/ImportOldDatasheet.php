<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Project;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportOldDatasheet extends Job // implements ShouldQueue
{
    //use InteractsWithQueue, SerializesModels;

    /**
     * @var Project
     */
    protected $project;

    protected $file;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);

        foreach ($rows as $row) {

        }
    }
}
