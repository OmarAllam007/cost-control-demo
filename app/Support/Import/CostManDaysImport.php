<?php

namespace App\Support\Import;

use App\BreakDownResourceShadow;
use App\CostManDay;
use App\Period;
use App\Project;
use Illuminate\Support\Collection;

class CostManDaysImport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    private $period;

    /** @var Collection */
    private $activities;

    private $headers = [];

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;

        $this->activities = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('DISTINCT code, wbs_id, activity_id')
            ->get()
            ->keyBy(function ($res) {
                return strtolower($res->code);
            });
    }

    function import($filename)
    {
        $sheet = \PHPExcel_IOFactory::load($filename)->getSheet(0);

        $this->headers = [
            $sheet->getCell('A1')->getValue(),
            $sheet->getCell('B1')->getValue(),
            $sheet->getCell('C1')->getValue(),
        ];

        $rows = $sheet->getRowIterator(2);

        $failed = collect();
        $success = 0;

        CostManDay::unguard();

        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $data = [];
            /**
             * @var string $c
             * @var \PHPExcel_Cell $cell
             */
            foreach ($cells as $c => $cell) {
                $data[$c] = $cell->getValue();
            }

            if (!array_filter($data)) {
                continue;
            }

            $code = strtolower($data['A']);
            if (!$this->activities->has($code)) {
                $data['D'] = 'Invalid code';
                $failed->push($data);
                continue;
            }

            $activity = $this->activities->get($code);
            $conditions = ['wbs_id' => $activity->wbs_id, 'activity_id' => $activity->activity_id, 'period_id' => $this->period->id];
            $attributes = ['actual' => floatval($data['B']), 'progress' => floatval($data['C'])];

            CostManDay::updateOrCreate($conditions, $attributes);

            ++$success;
        }

        $result = ['success' => $success, 'failed' => false];

        if ($failed->count()){
            $result['failed'] = $this->generateFailed($failed);
        }

        return $result;
    }

    private function generateFailed($failed)
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getSheet(0);

        $this->headers[] = "Errors";
        $sheet->fromArray($this->headers, null, "A1", true);

        $counter = 1;
        foreach ($failed as $row) {
            ++$counter;
            $sheet->fromArray($row, null, "A{$counter}", true);
        }

        $filename = storage_path('app/public/' . uniqid('cost_man_days_') . '.xlsx');
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return "/storage/" . basename($filename);
    }

}