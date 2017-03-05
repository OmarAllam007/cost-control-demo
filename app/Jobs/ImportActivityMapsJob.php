<?php

namespace App\Jobs;

use App\ActivityMap;
use App\Project;
use Illuminate\Support\Collection;

class ImportActivityMapsJob extends ImportJob
{
    /**
     * @var Project
     */
    protected $project;

    /**
     * @var
     */
    protected $file;

    /**
     * @var Collection
     */
    protected $codes;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->loadCodes();
    }


    public function handle()
    {
        $loader = new \PHPExcel_Reader_Excel2007();
        $excel = $loader->load($this->file);
        $sheet = $excel->getSheet(0);
        $rows = $sheet->getRowIterator(2);

        $success = 0;

        $failed = collect();
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }

            $code = strtolower(trim($data[0]));
            if ($this->codes->search($code) !== false && $data[1]) {
                ActivityMap::updateOrCreate([
                    'project_id' => $this->project->id, 'activity_code' => $data[0], 'equiv_code' => $data[1]
                ]);

                ++$success;
            } else {
                $failed->push($data);
            }
        }
        return compact('success', 'failed');
    }

    protected function loadCodes()
    {
        $this->codes = $this->project->breakdown_resources()
            ->pluck('breakdown_resources.code')->map(function($code) {
                return strtolower(trim($code));
            })->unique();

        return $this->codes;
    }
}
