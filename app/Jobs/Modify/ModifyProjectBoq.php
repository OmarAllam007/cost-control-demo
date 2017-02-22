<?php

namespace App\Jobs\Modify;

use App\Boq;
use App\Jobs\ImportJob;
use App\Jobs\Job;
use App\WbsLevel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyProjectBoq extends ImportJob
{

    protected $file;


    protected $project;

    protected $boqs;
    protected $wbs_levels;


    public function __construct($file, $project)
    {
        $this->file = $file;
        $this->project = $project;
        $this->wbs_levels = WbsLevel::where('project_id', $project->id)->get()->keyBy('code')->map(function ($level) {
            return $level->id;
        });
    }

    public function handle()
    {
        set_time_limit(600);
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $rows = $excel->getSheet(0)->getRowIterator(2);
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }
            $level = $this->wbs_levels->get($data[13]);
            \DB::beginTransaction();
            \DB::update('UPDATE boqs
SET description = ?, type = ? , unit_id = ? , quantity =? ,
  price_ur = ? , dry_ur = ? , kcc_qty = ? , materials = ? , subcon = ? , manpower = ?  WHERE project_id = ?  AND wbs_id = ? AND boqs.cost_account = ? ', [$data[2], $data[3], $this->getUnit($data[4]), $data[5], $data[6]
                , $data[7], $data[8], $data[9], $data[10], $data[11], $this->project->id, $level, $data[1]]
            );
            \DB::commit();

        }
    }
}