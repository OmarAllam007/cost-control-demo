<?php
namespace App\Jobs;

use App\BreakDownResourceShadow;
use App\Project;
use function array_values;
use function basename;
use function compact;
use function floatval;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Row;
use function storage_path;

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

    /**
     * @param Project $project
     * @param string $file
     */
    public function __construct($project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->failed = collect();
        $this->success = 0;
    }

    /**
     * @throws \PHPExcel_Exception
     */
    function handle()
    {
        $excel = PHPExcel_IOFactory::load($this->file);
        $sheet = $excel->getSheet();

        $rows = $sheet->getRowIterator(2);
        foreach ($rows as $row) {
            $data = $this->parseRow($row);
            $resource = $this->project->shadows()->where('code', $data['A'])->where('resource_code', $data['B'])->first();
            if (!$resource) {
                $data['D'] = 'Resource not found';
                $this->failed->push($data);
                continue;
            }

            $progress = floatval($data['C']);
            $status = 'In progress';
            if ($progress == 0) {
                $status = 'Not Started';
            } elseif ($progress == 100) {
                $status = 'Closed';
            }

            $resource->update(compact('progress', 'status'));
            ++ $this->success;
        }

        $result = ['success' => $this->success, 'failed' => ''];
        if ($this->failed->count()) {
            $result['failed'] = $this->createFailedExcel();
        }

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
            "Activity Code", "Resource Code", "Progress", "Error"
        ], null, "A1", true);

        foreach ($this->failed as $row) {
            $sheet->fromArray(array_values($row), null, "A{$count}", true);
            ++$count;
        }

        $filename = storage_path('app/public/update_progress_failed_' . date('YmdHis') . '.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return '/storage/' . basename($filename);
    }
}