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
            'App ID', 'WBS Code', 'Activity Code', 'Activity', 'Cost Account', 'Resource Name', 'Resource Code',
            'Productivity Ref', 'Labour Count', 'Equation', 'Remarks', 'Important'
        ];

        $sheet->fromArray($headers, '', "A1", true);

        $this->project->shadows()->budgetOnly()->with(['breakdown_resource', 'wbs'])
            ->chunk(1000, function ($shadows) use ($sheet) {
                foreach ($shadows as $shadow) {
                    ++$this->counter;

                    $row = [
                        $shadow->breakdown_resource_id, // A
                        $shadow->wbs->code,             // B
                        $shadow->code,                  // C
                        $shadow->activity,              // D
                        $shadow->cost_account,          // E
                        $shadow->resource_name,         // F
                        $shadow->resource_code,         // G
                        $shadow->productivity_ref,      // H
                        $shadow->labors_count,          // I
                        $shadow->breakdown_resource->equation, // J
                        $shadow->remarks,                      // K
                        $shadow->important? '*' : ''           // L
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
        $sheet->getStyle('A1:L1')->applyFromArray([
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

            $sheet->getStyle("B$row:L$row")->applyFromArray([
                'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => $color]]
            ]);
        }

        $sheet->freezePane('A2');
        foreach (range('A', 'L') as $c) {
            if ($c != 'F') {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }
        }

        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(50);
        $sheet->setAutoFilter("A1:L{$this->counter}");
    }
}