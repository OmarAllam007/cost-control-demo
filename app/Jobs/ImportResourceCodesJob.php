<?php

namespace App\Jobs;

use App\Project;
use App\Resources;

class ImportResourceCodesJob extends ImportJob
{
    /**  @var string */
    private $file;

    /** @var \Illuminate\Support\Collection */
    protected $codes;

    /** @var integer */
    private $project_id;

    public function __construct($file, $project_id = null)
    {
        $this->file = $file;
//        $this->codes = collect();
//        $this->loadCodes();
        $this->project_id = $project_id;
    }

    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);

        $project = Project::find($this->project_id);
        $result = ['success' => 0, 'failed' => collect(), 'project' => $project];
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }

            if ($data[0] && $data[1]) {
                $code = mb_strtolower($data[0]);
                $resource = Resources::where(['resource_code' => $code, 'project_id' => $this->project_id])->first();
                if ($resource) {
                    $resource->codes()->updateOrCreate(['code' => $data[1], 'project_id' => $this->project_id]);
                    ++$result['success'];
                } else {
                    $result['failed']->push($data);
                }
            }
        }

        return $result;
    }
}
