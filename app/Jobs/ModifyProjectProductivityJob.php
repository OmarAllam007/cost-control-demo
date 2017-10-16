<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Productivity;
use App\Project;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModifyProjectProductivityJob extends Job
{
    /** @var Project */
    protected $project;

    protected $rules = ['csi_code' => 'required', 'reduction_factor' => 'required|numeric|gte:0'];

    protected $file;

    /** @var \Illuminate\Support\Collection */
    protected $failed;

    /** @var \Illuminate\Support\Collection */
    protected $productivity;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->failed = collect();
    }

    public function handle()
    {
        $this->loadProductivity();

        $excel = \PHPExcel_IOFactory::load($this->file);
        $sheet = $excel->getSheet();

        $rows = $sheet->getRowIterator(2);
        $count = 0;
        foreach ($rows as $row) {
            $cells = $row->getCellIterator();
            $record = $this->getDataFromCells($cells);

            $data = ['csi_code' => strtolower($record['A']), 'reduction_factor' => $record['G']];
            $validator = \Validator::make($data, $this->rules);

            if ($validator->fails()) {
                $record["J"] = implode(PHP_EOL, $validator->messages()->all());
                $this->failed->push($record);
                continue;
            }

            $productivity = $this->productivity->get($data['csi_code']);
            if (!$productivity) {
                $record["J"] = "Productivity not found";
                $this->failed->push($record);
                continue;
            }

            $productivity->reduction_factor = $data['reduction_factor'];
            $productivity->save();
            ++$count;
        }

        $failed = false;
        if ($this->failed->count()) {
            $failed = $this->prepareFailedFile();
        }

        return ['count' => $count, 'failed' => $failed];
    }

    protected function getDataFromCells(\PHPExcel_Worksheet_CellIterator $cells)
    {
        $data = [];
        /**
         * @var string $idx
         * @var \PHPExcel_Cell $cell
         */
        foreach ($cells as $idx => $cell) {
            $data[$idx] = $cell->getValue();
        }

        return $data;
    }

    protected function loadProductivity()
    {
        return $this->productivity = Productivity::where(['project_id' => $this->project->id])
            ->get()->keyBy(function ($productivity) {
                return strtolower($productivity->csi_code);
            });
    }

    protected function prepareFailedFile()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getSheet(0);

        $headers = ['Code',	'Category', 'Name',	'Description', 'Crew Structure', 'Daily Output', 'After Reduction', 'Reduction Factor', 'Unit',	'Source', 'Error'];
        $sheet->fromArray($headers, '', 'A1');
        $sheet->getStyle('A1:I1')->applyFromArray(['font' => ['bold' => true]]);

        $rowCount = 2;
        foreach ($this->failed as $item) {
            $sheet->fromArray(array_values($item), '', "A{$rowCount}");
            ++$rowCount;
        }

        foreach (['A', 'E', 'F', 'G', 'H', 'I'] as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        foreach (['B', 'C', 'D'] as $c) {
            $sheet->getColumnDimension($c)->setWidth(50);
        }

        $sheet->getStyle("E2:G{$rowCount}")
            ->getNumberFormat()
            ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $filename = storage_path("app/public/productivity_{$this->project->id}_" . uniqid() . '.xlsx');
        $writer->save($filename);

        return '/storage/' . basename($filename);
    }
}
