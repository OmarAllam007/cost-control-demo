<?php

namespace App\Jobs;

use App\Project;
use App\WbsLevel;

class WbsImportJob extends Job
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var
     */
    private $file;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     * @param $file
     */
    public function __construct(Project $project, $file)
    {
        //
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

        $levels = collect();
        $this->project->wbs_levels()->get()->each(function($level) use ($levels) {
            $levels->put($level->canonical, $level->id);
        });

        $rows = $sheet->getRowIterator();
        foreach ($rows as $row) {

            $cells = $row->getCellIterator();
            $parent = 0;
            /** @var \PHPExcel_Cell $cell */
            $code = '';
            $path = [];
            foreach ($cells as $index => $cell) {
                if ($index == 'A') {
                    $code = $cell->getValue();
                    continue;
                }

                $value = $cell->getValue();
                if (!$value) {
                    continue;
                }

                $path[] = strtolower($value);
                $canonical = implode('/', $path);
                if ($levels->has($canonical)) {
                    $parent = $levels->get($canonical);
                    continue;
                }

                $level = WbsLevel::create([
                    'code' => $code,
                    'name' => $value,
                    'parent_id' => $parent,
                    'project_id' => $this->project->id
                ]);
                $parent = $level->id;
                $levels->put($canonical, $parent);
            }
        }

        unlink($this->file);
    }
}
