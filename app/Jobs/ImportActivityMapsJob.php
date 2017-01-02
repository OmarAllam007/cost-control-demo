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

        $counter = 0;

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = $this->getDataFromCells($cells);

            if (!array_filter($data)) {
                continue;
            }

            $code = mb_strtolower($data[0]);
            if ($this->codes->has($code) && $data[1]) {
                ActivityMap::updateOrCreate([
                    'project_id' => $this->project->id, 'activity_code' => $data[0], 'equiv_code' => $data[1]
                ]);

                ++$counter;
            }
        }

        return $counter;
    }

    protected function loadCodes()
    {
        $codes = $this->project->breakdown_resources()
            ->pluck('breakdown_resources.id', 'breakdown_resources.code');

        $keys = array_map('strtolower', $codes->keys()->toArray());
        $values = $codes->values()->toArray();

        $this->codes = collect(array_combine($keys, $values));

        return $this->codes;
    }
}
