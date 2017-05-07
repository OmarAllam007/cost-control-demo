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
        $this->wbs_levels = WbsLevel::select('id', 'code')->where('project_id', $project->id)
            ->get()->keyBy(function ($level) {
                return trim(strtolower($level->code));
            })->map(function($level){
                return $level->id;
            });
    }

    public function handle()
    {
        set_time_limit(600);
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $rows = $excel->getSheet(0)->getRowIterator(2);

        $counter = 0;
        \DB::beginTransaction();

        $failed = collect();

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }

            $wbs_code = trim(strtolower($data[13]));
            $level = $this->wbs_levels->get($wbs_code);

            $conditions = ['project_id' => $this->project->id, 'wbs_id' => $level, 'cost_account' => $data[1]];
            if (!Boq::where($conditions)->exists()) {
                $failed->push($data);
                continue;
            }

            $result = \DB::update('UPDATE boqs
              SET item_code=:item_code, description = :description, type = :type, unit_id = :unit_id, 
              quantity = :quantity,
              price_ur = :price_ur , dry_ur = :dry_ur, kcc_qty = :kcc_qty, materials = :material , 
              subcon = :subcon , manpower = :manpower  WHERE 
              project_id = :project_id  AND wbs_id = :wbs_id AND cost_account LIKE :cost_account', [
                    'item_code' => $data[0], 'description' => $data[2],
                    'type' => $data[3], 'unit_id' => $this->getUnit($data[4]), 'quantity' => $data[5], 'price_ur' => $data[6], 'dry_ur' => $data[7],
                    'kcc_qty' => $data[8], 'material' => $data[9], 'subcon' => $data[10], 'manpower' => $data[11],
                    'project_id' => $this->project->id, 'wbs_id' => $level, 'cost_account' => $data[1]
                ]
            );


            ++$counter;

        }
        \DB::commit();

        $content = $failed->map(function ($row) { return implode(',', array_map('csv_quote', $row)); })->implode(PHP_EOL);
        $filename = storage_path('app/modify_boq_' . $this->project->id . '.csv');
        file_put_contents($filename, $content);

        return $counter;
    }
}
