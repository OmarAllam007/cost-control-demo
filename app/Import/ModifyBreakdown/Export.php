<?php

namespace App\Import\ModifyBreakdown;


use App\Project;
use function array_reverse;
use Illuminate\Support\Collection;

class Export
{
    /** @var Project */
    private $project;
    /** @var Collection */
    private $divisions;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->counter = 1;
        $this->divisions = collect();
    }

    public function handle()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        $headers = [
            'App ID', 'WBS Code', 'Activity Code', 'Activity', 'Cost Account', 'Resource Name', 'Resource Code',
            'Productivity Ref', 'Labour Count', 'Equation', 'Remarks', 'Driving',
            'Breakdown Template', 'Activity Division 1', 'Activity Division 2', 'Activity Division 3',
            'Activity Division 4'
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
                        $shadow->important? '*' : '',          // L,
                        $shadow->template,                     // M
                    ];

                    $sheet->fromArray($row, '', "A{$this->counter}", true);

                    $divisions = $this->getDivisions($shadow);

                    $sheet->fromArray($divisions, '', "N{$this->counter}", true);
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
        $sheet->getStyle('A1:Q1')->applyFromArray([
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

            $sheet->getStyle("B$row:Q$row")->applyFromArray([
                'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => $color]]
            ]);
        }

        $sheet->freezePane('A2');
        foreach (range('A', 'L') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        foreach (range('M', 'Q') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(false)->setWidth(50);
        }

        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(50);
        $sheet->getColumnDimension('K')->setAutoSize(false)->setWidth(50);
        $sheet->setAutoFilter("A1:Q{$this->counter}");
    }

    private function getDivisions($shadow)
    {
        if ($this->divisions->has($shadow->activity_id)) {
            return $this->divisions->get($shadow->activity_id);
        }

        $parent = $shadow->std_activity->division;
        $divisions = [];

        while ($parent) {
            $divisions[] = $parent->label;
            $parent = $parent->parent;
        }

        $divisions = array_reverse($divisions);
        $this->divisions->put($shadow->activity_id, $divisions);

        return $divisions;
    }
}