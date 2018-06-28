<?php
namespace App\Jobs;

use App\BreakDownResourceShadow;
use App\Project;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Row;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class UpdateProjectProgress extends Job
{
    /** @var Project */
    private $project;

    /** @var string */
    private $file;

    /** @var \Illuminate\Support\Collection */
    private $failed;

    /** @var int */
    private $success;

    /** @var Collection */
    private $wbs_codes;

    /**
     * @param Project $project
     * @param string $file
     */
    public function __construct($project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->failed = collect();
        $this->loadWbs();
    }

    /**
     * @throws \PHPExcel_Exception
     */
    function handle()
    {
        $excel = PHPExcel_IOFactory::load($this->file);
        $sheet = $excel->getSheet();

        $rows = $sheet->getRowIterator(2);
        $records = collect();
        foreach ($rows as $row) {
            $data = $this->parseRow($row);

            $wbs_code = strtolower($data['A']);
            $code = $data['B'];
            $progress = floatval($data['D']);

            $wbs_id = $this->wbs_codes->get($wbs_code);
            if (!$wbs_id) {
                $data['E'] = 'WBS not found';
                $this->failed->push($data);
                continue;
            }

            $resources = BreakDownResourceShadow::where(compact('wbs_id', 'code'))->where('show_in_cost', 1)->get();
            if (!$resources->count()) {
                $data['E'] = 'Invalid activity code';
                $this->failed->push($data);
                continue;
            }

            $activity = new Fluent(compact('code', 'progress', 'resources'));
            $records->put($code, $activity);
           
            ++ $this->success;
        }

        

        $result = compact('records');
        if ($this->failed->count()) {
            $result['failed'] = $this->createFailedExcel();
        }

        \Cache::put("update_progress_{$this->project->id}", $result, 3600);

        return $result;
    }

    /**
     * @param PHPExcel_Worksheet_Row $row
     * @return array
     */
    private function parseRow($row)
    {
        $cells = $row->getCellIterator();
        $data = [];

        foreach ($cells as $col => $cell) {
            /** @var \PHPExcel_Cell $cell */
            $data[$col] = $cell->getValue();
        }

        return $data;
    }

    /**
     * @throws \PHPExcel_Exception
     */
    private function createFailedExcel()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getSheet();
        $count = 2;

        $sheet->fromArray([
            "WBS Code", "Activity Code", "Activity", "Progress", "Error"
        ], null, "A1", true);

        foreach ($this->failed as $row) {
            $sheet->fromArray(array_values($row), null, "A{$count}", true);
            ++$count;
        }

        $filename = storage_path('app/public/update_progress_failed_' . date('YmdHis') . '.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return '/storage/' . basename($filename);
    }

    private function loadWbs()
    {
        $this->wbs_codes = $this->project->wbs_levels()->select(['id', 'code'])->get()->map(function($level) {
            $level->code = strtolower($level->code);
            return $level;
        })->pluck('id', 'code');
    }
}