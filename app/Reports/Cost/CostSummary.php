<?php

namespace App\Reports\Cost;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\CostConcerns;
use App\CostShadow;
use App\Http\Controllers\CostConcernsController;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\ResourceType;
use App\StdActivity;
use Illuminate\Support\Collection;

class CostSummary
{
    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Period
     */
    protected $period;

    function __construct(Period $period)
    {

        $this->project = $period->project;
        $this->period = $period;
    }

    function run()
    {
        $project = $this->project;
        /** @var Collection $general_activities */
        $general_activities = StdActivity::where('division_id', 779)->pluck('id');
        $fields = [
            'sum(budget_cost) budget_cost', 'sum(prev_cost) as previous_cost' ,'sum(to_date_cost) as to_date_cost', 'sum(allowable_ev_cost) as ev',
            'sum(allowable_var) as to_date_var', 'sum(remaining_cost) as remaining_cost',
            'sum(completion_cost) as completion_cost', 'sum(cost_var) as completion_cost_var'
        ];

        $toDateData = MasterShadow::where('period_id', $this->period->id)
            ->selectRaw('(CASE WHEN activity_id IN (' . $general_activities->implode(', ') . ") THEN 'INDIRECT COST' WHEN activity_id = 3060 THEN 'MANAGEMENT RESERVE' ELSE 'DIRECT COST' END) as type")
            ->selectRaw(implode(', ', $fields))
            ->groupBy('type')
            ->orderBy('type')
            ->get()->keyBy('type');

        if ($toDateData->has('MANAGEMENT RESERVE')) {
            $reserve = $toDateData->get('MANAGEMENT RESERVE');
            if ($reserve->budget_cost) {
                $reserve->completion_cost = $reserve->remaining_cost = 0;
                $reserve->completion_cost_var = $reserve->budget_cost;

                $progress = min(1, $toDateData->sum('to_date_cost') / ($toDateData->sum('budget_cost') - $reserve->budget_cost));
                $reserve->to_date_var = $reserve->allowable_cost = $progress * $reserve->budget_cost;
            }
        }

        return compact('toDateData', 'project', 'resourceTypes');
    }

    function excel()
    {
        $excel = new \PHPExcel();

        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet());
        $filename = storage_path('app/cost-summary-' . uniqid() . '.xlsx');
        $writer = new \PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save($filename);

        $name = slug($this->project->name) . '_' . slug($this->period->name) . '_cost-summary.xlsx';
        return \Response::download($filename, $name)->deleteFileAfterSend(true);
    }

    function sheet()
    {
        $data = $this->run();
        $toDateData = $data['toDateData'];
        $project = $this->project;

        $excel = \PHPExcel_IOFactory::load(storage_path('templates/cost-summary.xlsx'));
        $sheet = $excel->getSheet(0);

        $varCondition = new \PHPExcel_Style_Conditional();
        $varCondition->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS);
        $varCondition->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_LESSTHAN);
        $varCondition->addCondition(0);
        $varCondition->getStyle()->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_RED);

        $projectCell = $sheet->getCell('A4');
        $issueDateCell = $sheet->getCell('A5');

        $projectCell->setValue($projectCell->getValue() . ' ' . $project->name);
        $issueDateCell->setValue($issueDateCell->getValue() . ' ' . date('d M Y'));

        $logo = imagecreatefrompng(public_path('images/kcc.png'));
        $drawing = new \PHPExcel_Worksheet_MemoryDrawing();
        $drawing->setName('Logo')->setImageResource($logo)
            ->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
            ->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
            ->setCoordinates('H2')->setWorksheet($sheet);

        $start = 11;
        $counter = $start;
        foreach ($toDateData as $typeToDateData) {
            $row = [
                $typeToDateData['type'],
                $typeToDateData['budget_cost'] ?? '0.00',
                $typeToDateData['previous_cost'] ?? '0.00',
                $typeToDateData['to_date_cost'] ?? '0.00',
                $typeToDateData['ev'] ?? '0.00',
                $typeToDateData['to_date_var'] ?? '0.00',
                $typeToDateData['remaining_cost'] ?? '0.00',
                $typeToDateData['completion_cost'] ?? '0.00',
                $typeToDateData['completion_cost_var'] ?? '0.00',
            ];

            $sheet->fromArray($row, null, "A{$counter}", true);
            ++$counter;
        }

        $row = [
            "Totals",
            $toDateData->sum('budget_cost'),
            $toDateData->sum('previous_cost'),
            $toDateData->sum('to_date_cost'),
            $toDateData->sum('ev'),
            $toDateData->sum('to_date_var'),
            $toDateData->sum('remaining_cost'),
            $toDateData->sum('completion_cost'),
            $toDateData->sum('completion_cost_var'),
        ];
        $sheet->fromArray($row, null, "A{$counter}", true);
        $sheet->setCellValue("A{$counter}", "Total");

        $sheet->getStyle("A{$start}:A{$counter}")->getFont()->setBold(true);
        $totalsStyles = $sheet->getStyle("A{$counter}:I{$counter}");
        $totalsStyles->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new \PHPExcel_Style_Color('DAEEF3'));
        $totalsStyles->getFont()->setBold(true);

        $sheet->getStyle("B{$start}:I{$counter}")->getNumberFormat()->getBuiltInFormatCode(38); //->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
//        $sheet->getStyle("E{$start}:E{$counter}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("H{$start}:H{$counter}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("K{$start}:K{$counter}")->setConditionalStyles([$varCondition]);


        //<editor-fold defaultstate="collapsed desc="Budget Cost VS Completion Cost Chart">
        $end = $counter - 1;

        $xAxisLabels = [
            new \PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!A{$start}:A{$end}", null, $counter - $start)
        ];

        $budgetVsCompletionValues = [
            new \PHPExcel_Chart_DataSeriesValues('Number', "'{$sheet->getTitle()}'!B$start:B$end"),
            new \PHPExcel_Chart_DataSeriesValues('Number', "'{$sheet->getTitle()}'!J$start:J$end"),
        ];

        $budgetVsCompletionLabels = [
            new \PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!B" . ($start - 1), NULL, 1),
            new \PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!H" . ($start - 1), NULL, 1),
        ];

        $budgetVsCompletionTitle = new \PHPExcel_Chart_Title('Budget Cost vs At Completion');
        $budgetVsCompletionLegend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);
        $budgetVsCompletionDataSeries = new \PHPExcel_Chart_DataSeries(
            \PHPExcel_Chart_DataSeries::TYPE_BARCHART,
            \PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
            [0, 1],
            $budgetVsCompletionLabels,
            $xAxisLabels,
            $budgetVsCompletionValues
        );
        $budgetVsCompletionDataSeries->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

        $budgetVsCompletionPlot = new \PHPExcel_Chart_PlotArea(null, [$budgetVsCompletionDataSeries]);
        $budgetVsCompletionChart = new \PHPExcel_Chart(
            'budget_vs_comp', $budgetVsCompletionTitle, $budgetVsCompletionLegend,
            $budgetVsCompletionPlot, true, '0', null, null
        );

        $budgetVsCompletionChart->setTopLeftCell("A" . ($counter + 5))->setBottomRightCell('E' . ($counter + 25));
        $sheet->addChart($budgetVsCompletionChart);
//</editor-fold>

        //<editor-fold defaultstate="collapsed desc="To Date Vs Allowable Chart">
        $todateVsAllowableValues = [
            new \PHPExcel_Chart_DataSeriesValues('Number', "'{$sheet->getTitle()}'!D$start:D$end"),
            new \PHPExcel_Chart_DataSeriesValues('Number', "'{$sheet->getTitle()}'!E$start:E$end"),
        ];

        $todateVsAllowableLabels = [
            new \PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!D" . ($start - 1), NULL, 1),
            new \PHPExcel_Chart_DataSeriesValues('String', "'{$sheet->getTitle()}'!E" . ($start - 1), NULL, 1),
        ];

        $todateVsAllowableTitle = new \PHPExcel_Chart_Title('To Date vs Allowable');
        $todateVsAllowableLegend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);
        $todateVsAllowableDataSeries = new \PHPExcel_Chart_DataSeries(
            \PHPExcel_Chart_DataSeries::TYPE_BARCHART,
            \PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
            [0, 1],
            $todateVsAllowableLabels,
            $xAxisLabels,
            $todateVsAllowableValues
        );
        $todateVsAllowableDataSeries->setPlotDirection(\PHPExcel_Chart_DataSeries::DIRECTION_BAR);

        $todateVsAllowablePlot = new \PHPExcel_Chart_PlotArea(null, [$todateVsAllowableDataSeries]);
        $todateVsAllowableChart = new \PHPExcel_Chart(
            'budget_vs_comp', $todateVsAllowableTitle, $todateVsAllowableLegend,
            $todateVsAllowablePlot, true, '0', null, null
        );

        $todateVsAllowableChart->setTopLeftCell("F" . ($counter + 5))->setBottomRightCell('J' . ($counter + 25));
        $sheet->addChart($todateVsAllowableChart);
        //</editor-fold>

        $sheet->setShowGridlines(false);

        return $sheet;
    }
}