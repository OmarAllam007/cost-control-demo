<?php

namespace App\Import\ModifyBreakdown;


use App\Project;

class Export
{
    /** @var Project */
    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->counter = 1;
    }

    public function handle()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        $headers = [
            'App ID', 'WBS Code', 'Activity Code', 'Activity', 'Resource Name', 'Resource Code',
            'Productivity Ref', 'Labour Count', 'Equation', 'Remarks', 'Important'
        ];

        $sheet->fromArray($headers, '', "A1", true);

        $this->project->shadows()->budgetOnly()->with(['breakdown_resource', 'wbs'])
            ->chunk(1000, function ($shadows) use ($sheet) {
                foreach ($shadows as $shadow) {
                    ++$this->counter;

                    $row = [
                        $shadow->breakdown_resource_id,
                        $shadow->wbs->code,
                        $shadow->code,
                        $shadow->activity,
                        $shadow->resource_name,
                        $shadow->resource_code,
                        $shadow->productivity_ref,
                        $shadow->labors_count,
                        $shadow->breakdown_resource->equation,
                        $shadow->remarks,
                        $shadow->important? '*' : ''
                    ];

                    $sheet->fromArray($row, '', "A{$this->counter}", true);
                }
            });

        $this->setStyles($sheet);

        $filename = storage_path("app/modify_breakdown_{$this->project->id}.xlsx");
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return $filename;
    }

    /**
     * @param $sheet
     */
    private function setStyles($sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'type' => 'solid',
                'startcolor' => ['rgb' => '3490DC'],
            ]
        ]);

        $sheet->getStyle("A2:A{$this->counter}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'type' => 'solid',
                'startcolor' => ['rgb' => 'F9ACAA'],
            ]
        ]);

        for ($row = 2; $row <= $this->counter; ++$row) {
            $color = $row % 2? 'BCDEFA' : 'EFF8FF';

            $sheet->getStyle("B$row:K$row")->applyFromArray([
                'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => $color]]
            ]);
        }

        $sheet->freezePane('A2');
        foreach (range('A', 'K') as $c) {
            if ($c != 'E') {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }
        }

        $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(50);
        $sheet->setAutoFilter("A1:K{$this->counter}");
    }
}