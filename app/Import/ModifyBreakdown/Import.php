<?php
namespace App\Import\ModifyBreakdown;

use App\BreakdownResource;
use App\Productivity;
use App\Project;
use App\Resources;

class Import
{
    /** @var Project */
    private $project;
    private $file;

    public function __construct(Project $project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->rows = collect();
        $this->failed = collect();
        $this->success = 0;
    }

    function handle()
    {
        $iterator = \PHPExcel_IOFactory::load($this->file)->getSheet(0)->getRowIterator();
        $this->rows = collect();

        foreach ($iterator as $index => $row) {
            if ($index == 1) {
                $this->headers = $this->getRowData($row);
                continue;
            }

            $this->rows->push($this->getRowData($row));

            if ($this->rows->count() >= 500) {
                $this->handleRows();
                $this->rows = collect();
            }
        }

        $this->handleRows();

        return $this->status();
    }

    private function getRowData($row)
    {
        $data = [];
        $cells = $row->getCellIterator();
        foreach ($cells as $column => $cell) {
            $data[$column] = $cell->getValue();
        }
        return $data;
    }

    private function handleRows()
    {
        $this->breakdownResources = BreakdownResource::budgetOnly()->where('project_id', $this->project->id)
            ->with('shadow')->find($this->rows->pluck('A')->toArray())->keyBy('id');

        $this->resources = Resources::whereNull('project_id')
            ->whereIn('resource_code', $this->rows->pluck('G')->unique())
            ->get()->keyBy(function ($resource) {
                return strtolower($resource->resource_code);
            });

        $this->productivities = Productivity::whereNull('project_id')
            ->whereIn('csi_code', $this->rows->pluck('H')->unique())
            ->get()->keyBy(function ($productivity) {
                return strtolower($productivity->csi_code);
            });

        $this->rows->each(function ($row) {
            $this->handleResource($row);
        });
    }

    private function handleResource($row)
    {
        $idx = intval($row['A']);
        $breakdownResource = $this->breakdownResources->get($idx);

        $attributes = [];

        $resource_code = strtolower($row['G']);
        if ($resource_code != strtolower($breakdownResource->shadow->resource_code)) {
            $attributes['resource_id'] = $this->resources->get($resource_code)->id ?? 0;
        }

        $productivity_code = strtolower($row['H']);
        if ($productivity_code) {
            if (strtolower($breakdownResource->shadow->productivity_ref) != $productivity_code) {
                $attributes['productivity_id'] = $this->productivities->get($productivity_code)->id ?? 0;
            }
        } else {
            $attributes['productivity_id'] = null;
        }

        $attributes['labor_count'] = $row['I'] ?: 0;
        $attributes['equation']= $row['J'] ?: 0;
        $attributes['remarks']= $row['K'];
        $attributes['important']= !empty($row['L']);
        $validator = \Validator::make($attributes, config('validation.breakdown_resource'));
        if ($validator->fails()) {
            $row['M'] = implode("\n", $validator->messages()->all());
            $this->failed->push($row);
            return false;
        }

        /** @var BreakdownResource $breakdownResource */
        $breakdownResource->forceFill($attributes)->save();
        ++$this->success;
        return true;
    }

    private function status(): array
    {
        $status = ['success' => $this->success, 'failed' => false];
        if ($this->failed->count()) {
            $status['failed'] = $this->createFailedFile();
        }

        return $status;
    }

    private function createFailedFile()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        $this->headers['L'] = 'Validation Errors';

        $sheet->fromArray($this->headers, null, "A1", true);
        $counter = 2;

        foreach ($this->failed as $row) {
            $sheet->fromArray($row, null, "A{$counter}", true);
            ++$counter;
        }

        $this->setStyles($sheet);

        $filepath = 'modify_breakdown_' . slug($this->project->name) . '_failed.xlsx';
        $filename = storage_path('app/public/' . $filepath);
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);

        return $filepath;
    }

    private function setStyles($sheet)
    {
        $counter = $this->failed->count() + 1;
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'type' => 'solid',
                'startcolor' => ['rgb' => '3490DC'],
            ]
        ]);

        $sheet->getStyle("A2:A{$counter}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'type' => 'solid',
                'startcolor' => ['rgb' => 'F9ACAA'],
            ]
        ]);

        for ($row = 2; $row <= $counter; ++$row) {
            $color = $row % 2 ? 'BCDEFA' : 'EFF8FF';

            $sheet->getStyle("B$row:M$row")->applyFromArray([
                'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => $color]]
            ]);
        }

        $sheet->freezePane('A2');
        foreach (range('A', 'M') as $c) {
            if ($c != 'F') {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }
        }

        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(50);
        $sheet->setAutoFilter("A1:M{$counter}");
    }
}