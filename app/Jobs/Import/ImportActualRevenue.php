<?php

namespace App\Jobs\Import;

use App\ActualRevenue;
use App\Jobs\ImportJob;
use App\Jobs\Job;
use App\WbsLevel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportActualRevenue extends ImportJob
{
    protected $file;
    protected $project_id;
    protected $project;
    protected $wbs_levels;

    public function __construct($file, $project)
    {
        $this->file = $file;
        $this->project = $project;
        $this->wbs_levels = WbsLevel::where('project_id', $project->id)
            ->get()->keyBy('code')->map(function ($level) {
                return $level->id;
            });

    }


    public function handle()
    {

        ini_set('max_execution_time', 500);
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            /** @var \PHPExcel_Cell $cell */
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }

            $wbs_level_id = $this->wbs_levels->get($data[0]);
            $cost_account = $data[1];
            $actual_revenue = $data[2];
            $actual_revenue_qty = $data[3];
            $period_id = $this->project->getMaxPeriod();

            ActualRevenue::create(['wbs_id'=>$wbs_level_id , 'cost_account'=>$cost_account,
            'value'=>$actual_revenue ,'quantity'=>$actual_revenue_qty, 'period_id'=>$period_id]);
        }


    }
}
