<?php

namespace App\Reports\Cost;

use App\CostConcern;
use App\Period;
use App\Project;
use Illuminate\Support\Collection;
use function json_decode;
use PHPExcel_Style_Borders;
use function str_replace;

class ConcernsReport
{
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    /** @var Collection */
    private $concerns;

    public function __construct($period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $this->concerns = CostConcern::where('period_id', $this->period->id)->get()->groupBy('report_name');
        $periods = $this->project->periods()->readyForReporting()->latest('end_date')->pluck('name', 'id');

        return ['concerns' => $this->concerns, 'project' => $this->project, 'period' => $this->period, 'periods' => $periods];
    }

    function excel()
    {
        $excel = new \PHPExcel();

        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet());
        $filename = storage_path('app/cost-summary-' . uniqid() . '.xlsx');
        $writer = new \PHPExcel_Writer_Excel2007($excel);

        $writer->save($filename);
        $excel = \PHPExcel_IOFactory::load(storage_path('templates/concerns-report.xlsx'));
        $sheet = $excel->getSheet(0);
        $name = slug($this->project->name) . '_' . slug($this->period->name) . '_issues_concerns.xlsx';
        return \Response::download($filename, $name)->deleteFileAfterSend(true);
    }

    /**
     * @return \PHPExcel_Worksheet
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    function sheet()
    {
        $this->run();
        $excel = \PHPExcel_IOFactory::load(storage_path('templates/concerns-report.xlsx'));
        $sheet = $excel->getSheet(0);

        $projectCell = $sheet->getCell('A4');
        $issueDateCell = $sheet->getCell('A5');

        $project = $this->project;
        $projectCell->setValue($projectCell->getValue() . ' ' . $project->name);
        $issueDateCell->setValue($issueDateCell->getValue() . ' ' . date('d M Y'));

        $logo = imagecreatefrompng(public_path('images/kcc.png'));
        $drawing = new \PHPExcel_Worksheet_MemoryDrawing();
        $drawing->setName('Logo')->setImageResource($logo)
            ->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
            ->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
            ->setCoordinates('H2')->setWorksheet($sheet);

        $start = 10;
        $counter = $start;
        $a_ord = ord('A');

        foreach ($this->concerns as $report => $group) {
            $sheet->mergeCells("A{$counter}:B{$counter}");
            $sheet->setCellValue("A{$counter}", $report);
            $sheet->getStyle("A{$counter}")
                ->getFont()->applyFromArray(['bold' => true, 'size' => 14, 'underline' => true]);
            $counter+=2;

            foreach ($group as $concern) {
                $concernStart = $counter;
                $data = json_decode($concern->data, true);
                $count = count($data);
                $last_cell = chr($a_ord + $count - 1);
                $sheet->mergeCells("A{$counter}:{$last_cell}{$counter}");
                $sheet->getCell("A{$counter}")->setValue($concern->comment);
                $sheet->getStyle("A{$counter}:{$last_cell}{$counter}")->getAlignment()->setWrapText(true);

                ++$counter;
                $next = $counter + 1;
                $col = 'A';
                foreach ($data as $key => $value) {
                    $cell = $sheet->getCell("{$col}{$counter}")->setValue($key);
                    $cell->getStyle()->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'EFF8FF']],
                        'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => '3490DC']]
                    ]);

                    $sheet->setCellValue("{$col}{$next}", $value);
                    ++$col;
                }

                $last_col = chr(ord($col) - 1);

                $sheet->getStyle("A{$concernStart}:{$last_col}{$next}")
                    ->getBorders()->getOutline()->setBorderStyle('medium');

                ++$concernStart;
                $sheet->getStyle("A{$concernStart}:{$last_col}{$next}")
                    ->getBorders()->getInside()->setBorderStyle('thin');

                $counter += 4;
            }

            ++$counter;
        }

        $sheet->setShowGridlines(false);

        return $sheet;
    }
}