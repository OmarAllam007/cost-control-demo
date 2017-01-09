<?php

namespace App\Jobs\Modify;

use App\ActivityDivision;
use App\Jobs\ImportJob;
use App\StdActivity;

class ModifyPublicStdActivitiesJob extends ImportJob
{

    protected $file;
    /**
     * @var Collection
     */
    protected $divisions;
    /**
     * @var Collection
     */
    protected $activities;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function handle()
    {

        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);

        $rows = $excel->getSheet(0)->getRowIterator(2);
        $displayOrder = 6;
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);
            if (!array_filter($data)) {
                continue;
            }
            $std_Activity = StdActivity::where('code', $data[0])->first();
            $division_id = ActivityDivision::where('name',$data[2])->first()->id;
            if ($std_Activity) {
                $item = [
                    'name' => $data[1],
                    'division_id' => $division_id,
                    'discipline' => $data[3],
                    'work_package_name' => $data[4],
                    'id_partial' => $data[5],
                ];
                $std_Activity->update($item);
                foreach ($std_Activity->variables->sortBy('display_order') as $variable) {
                    $variable->label = $data[$displayOrder];
                    $variable->update();
                    $displayOrder++;
                }

            }
            $displayOrder = 6;
        }

    }


}
