<?php

namespace App\Reports\Budget;

use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class CharterReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $resource_types;

    /** @var Collection */
    protected $disciplines;

    /** @var float */
    protected $total;

    /** @var int */
    protected $row = 1;

    function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {
        $this->total = BreakDownResourceShadow::where('project_id', $this->project->id)->budgetOnly()->sum('budget_cost');

        $this->resource_types = BreakDownResourceShadow::where('project_id', $this->project->id)->budgetOnly()
            ->selectRaw('resource_type as type, sum(budget_cost) as budget_cost')
            ->groupBy('resource_type')->orderBy('resource_type')
            ->get()->map(function ($type) {
                $type->weight = $type->budget_cost * 100 / $this->total;
                return $type;
            });

        $this->disciplines = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('a.discipline as discipline, sum(budget_cost) as budget_cost')->budgetOnly()
            ->join('std_activities as a', 'activity_id', '=', 'a.id')
            ->groupBy('a.discipline')->orderBy('a.discipline')
            ->get()->map(function ($discipline) {
                $discipline->weight = $discipline->budget_cost * 100 / $this->total;
                return $discipline;
            });

        $this->project->general_requirements = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->budgetOnly()
            ->where('resource_type_id', 1)
            ->selectRaw('sum(budget_cost) as cost')->value('cost');

        $this->project->management_reserve = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->budgetOnly()
            ->where('resource_type_id', 8)
            ->selectRaw('sum(budget_cost) as cost')->value('cost');

        $this->project->direct_cost = $this->total - $this->project->general_requirements - $this->project->management_reserve;

        $this->project->profit = floatval($this->project->project_contract_signed_value) + floatval($this->project->change_order_amount) - $this->total;

        return [
            'project' => $this->project, 'resource_types' => $this->resource_types,
            'disciplines' => $this->disciplines, 'total' => $this->total
        ];
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '-charter', function (LaravelExcelWriter $excel) {
            $excel->sheet('Project Charter', function($sheet) {
                $this->sheet($sheet);
            });
            $excel->download('xlsx');
        });
    }

    function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->mergeCells('A1:G1');

        $this->headerStyle = function (CellWriter $cells) {
            $cells->setAlignment('center')->setFont(['bold' => true])
                ->setFontColor('#FFFFFF')
                ->setBackground('#0091CF');
        };

        $this->subHeaderStyle = function (CellWriter $cells) {
            $cells->setFont(['bold' => true])
                ->setBackground('#CFFF91');
        };

        $this->addBasicInfo($sheet);
        $this->addDescription($sheet);
        $this->addBudgetByDiscipline($sheet);
        $this->addBudgetByResourceType($sheet);
        $this->addDisciplineBrief($sheet);
        $this->addAssumptions($sheet);

        $sheet->setAutoSize(false);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(30);
        $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(30);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->setBorder("A1:G{$this->row}", 'medium');
        $font = $sheet->getStyle("A1:G{$this->row}")->getFont()->getColor()->setRGB('171a1c');
        $sheet->setAutoFilter(true);
    }

    protected function addBasicInfo(LaravelExcelWorksheet $sheet)
    {
        $sheet->cells('A1', $this->headerStyle);

        $sheet->setCellValue('A1', 'Project Basic Info');

        foreach (range(2, 24) as $i) {
            $sheet->mergeCells("A{$i}:B{$i}");
            $sheet->mergeCells("C{$i}:G{$i}");
        }

        //<editor-fold defaultstate="collapsed" desc="Project basic info">
        $sheet->setCellValue('A2', 'Project Name');
        $sheet->setCellValue('C2', $this->project->name);
        $sheet->setCellValue('A3', 'Client Name');
        $sheet->setCellValue('C3', $this->project->client_name);
        $sheet->setCellValue('A4', 'Consultant Name');
        $sheet->setCellValue('C4', $this->project->consultant);
        $sheet->setCellValue('A5', 'Project Location');
        $sheet->setCellValue('C5', $this->project->project_location);
        $sheet->setCellValue('A6', 'Contract Type');
        $sheet->setCellValue('C6', $this->project->contract_type);
        $sheet->setCellValue('A7', 'Project Type');
        $sheet->setCellValue('C7', $this->project->project_type);
        $sheet->setCellValue('A8', 'Original Project Duration');
        $sheet->setCellValue('C8', $this->project->project_duration);
        $sheet->setCellValue('A9', 'Project Plan Start Sate');
        $sheet->setCellValue('C9', $this->project->project_start_date);
        $sheet->setCellValue('A10', 'Project Plan Finish Date');
        $sheet->setCellValue('C10', $this->project->expected_finish_date);

        $sheet->setCellValue('A11', 'Original Signed Contract Value ');
        $sheet->setCellValue('C11', $this->project->project_contract_signed_value);

        $sheet->setCellValue('A12', 'Tender Direct Cost');
        $sheet->setCellValue('C12', $this->project->tender_direct_cost);
        $sheet->setCellValue('A13', 'Tender Indirect Cost');
        $sheet->setCellValue('C13', $this->project->tender_indirect_cost);
        $sheet->setCellValue('A14', 'Tender Risk and Escalation');
        $sheet->setCellValue('C14', $this->project->tender_risk);
        $sheet->setCellValue('A15', 'Total Tender Amount');
        $sheet->setCellValue('C15', $this->project->tender_total_cost);
        $sheet->setCellValue('A16', 'Tender Initial Profit');
        $sheet->setCellValue('C16', $this->project->tender_initial_profit);
        $sheet->setCellValue('A17', 'Tender Initial Profitability Index');
        $sheet->setCellValue('C17', $this->project->tender_initial_profitability_index / 100);

        $sheet->setCellValue('A18', 'Project Direct Cost Budget');
        $sheet->setCellValue('C18', $this->project->direct_cost);
        $sheet->setCellValue('A19', 'Project General Requirement Budget');
        $sheet->setCellValue('C19', $this->project->general_requirements);
        $sheet->setCellValue('A20', 'Management Reserve');
        $sheet->setCellValue('C20', $this->project->management_reserve);
        $sheet->setCellValue('A21', 'Total Budget Cost');
        $sheet->setCellValue('C21', $this->project->budget_cost);

        $sheet->setCellValue('A22', 'EAC Contract Amount');
        $sheet->setCellValue('C22', $this->project->eac_contract_amount);
        $sheet->setCellValue('A23', 'Planned Profit Amount');
        $sheet->setCellValue('C23', $this->project->planned_profit_amount);
        $sheet->setCellValue('A24', 'Planned Profitability Index');
        $sheet->setCellValue('C24', $this->project->planned_profitability / 100);
        //</editor-fold>

        $sheet->cells('A1:A24', function ($cells) {
            $cells->setFont(['bold' => true]);
        })->setColumnFormat([
            'C11:C23' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ])->cells("A12:G17", function (CellWriter $cells) {
            $cells->setBackground('#EFF8FF');
        })->cells('A18:G21', function($cells) {
            $cells->setBackground('#CCE8FF');
        })->cells('A22:G24', function($cells) {
            $cells->setBackground('#A8D8FF');
        });

        $sheet->getStyle('C17')->getNumberFormat()->setFormatCode('0.00%');
        $sheet->getStyle('C24')->getNumberFormat()->setFormatCode('0.00%');

        $this->row = 25;
    }

    /**
     * @param LaravelExcelWorksheet $sheet
     */
    protected function addDescription(LaravelExcelWorksheet $sheet)
    {
        if (trim($this->project->description)) {
            $sheet->mergeCells("A{$this->row}:G" . ($this->row + 1));
            $this->row += 2;

            $sheet->mergeCells("A{$this->row}:G{$this->row}")
                ->setCellValue("A{$this->row}", 'Project Brief')
                ->cells("A{$this->row}", $this->headerStyle);

            $description = wordwrap($this->project->description, 95);
            $rows = array_map('trim', explode("\n", $description));

            foreach ($rows as $line) {
                $sheet->mergeCells("A{$this->row}:G{$this->row}")
                    ->getCell("A{$this->row}")
                    ->setValue($line)->getStyle()->getAlignment()->setWrapText(true);
                ++$this->row;
            }
        }
    }

    protected function addBudgetByDiscipline(LaravelExcelWorksheet $sheet)
    {
        $sheet->mergeCells("A{$this->row}:G" . ($this->row + 1));
        $this->row += 2;

        $sheet->mergeCells("A{$this->row}:G{$this->row}")
            ->setCellValue("A{$this->row}", 'Project Budget Summary')
            ->cells("A{$this->row}", $this->headerStyle);

        ++$this->row;
        $sheet->mergeCells("A{$this->row}:G{$this->row}")
            ->setCellValue("A{$this->row}", 'Project By Discipline')
            ->cells("A{$this->row}", $this->subHeaderStyle);

        ++$this->row;
        $sheet->mergeCells("A{$this->row}:E{$this->row}")
            ->setCellValue("A{$this->row}", 'Discipline')
            ->setCellValue("F{$this->row}", 'Budget Cost')
            ->setCellValue("G{$this->row}", 'Wt(%)')
            ->cells("A{$this->row}:G{$this->row}", function ($cells) {
                $cells->setFont(['bold' => true]);
            });

        $start = $this->row;
        foreach ($this->disciplines as $discipline) {
            ++$this->row;
            $sheet->mergeCells("A{$this->row}:E{$this->row}")
                ->setCellValue("A{$this->row}", $discipline->discipline);
            $sheet->setCellValue("F{$this->row}", $discipline->budget_cost);
            $sheet->setCellValue("G{$this->row}", $discipline->weight / 100);
        }

        ++$this->row;

        $sheet->mergeCells("A{$this->row}:E{$this->row}")
            ->setCellValue("A{$this->row}", 'Grand Total');
        $sheet->setCellValue("F{$this->row}", $this->disciplines->sum('budget_cost'));
        $sheet->setCellValue("G{$this->row}", 1);

        $sheet->setColumnFormat([
            "F{$start}:F{$this->row}" => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "G{$start}:G{$this->row}" => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00,
        ]);

        $sheet->cells("A{$this->row}:G{$this->row}", $this->subHeaderStyle);

        ++$this->row;
        $sheet->mergeCells("A{$this->row}:G{$this->row}");
        ++$this->row;
    }

    protected function addBudgetByResourceType(LaravelExcelWorksheet $sheet)
    {
        $sheet->mergeCells("A{$this->row}:G{$this->row}")
            ->setCellValue("A{$this->row}", 'Project By Resource Type')
            ->cells("A{$this->row}", $this->subHeaderStyle);

        ++$this->row;
        $sheet->mergeCells("A{$this->row}:E{$this->row}")
            ->setCellValue("A{$this->row}", 'Resource Type')
            ->setCellValue("F{$this->row}", 'Budget Cost')
            ->setCellValue("G{$this->row}", 'Wt(%)')
            ->cells("A{$this->row}:G{$this->row}", function ($cells) {
                $cells->setFont(['bold' => true]);
            });

        $start = $this->row;
        foreach ($this->resource_types as $type) {
            ++$this->row;
            $sheet->mergeCells("A{$this->row}:E{$this->row}")
                ->setCellValue("A{$this->row}", $type->type);
            $sheet->setCellValue("F{$this->row}", $type->budget_cost);
            $sheet->setCellValue("G{$this->row}", $type->weight / 100);
        }

        ++$this->row;
        $sheet->mergeCells("A{$this->row}:E{$this->row}")
            ->setCellValue("A{$this->row}", 'Grand Total');
        $sheet->setCellValue("F{$this->row}", $this->resource_types->sum('budget_cost'));
        $sheet->setCellValue("G{$this->row}", 1);

        $sheet->setColumnFormat([
            "F{$start}:F{$this->row}" => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            "G{$start}:G{$this->row}" => \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00,
        ]);

        $sheet->cells("A{$this->row}:G{$this->row}", $this->subHeaderStyle);
    }

    /**
     * @param LaravelExcelWorksheet $sheet
     */
    protected function addDisciplineBrief(LaravelExcelWorksheet $sheet)
    {
        if ($this->project->discipline_brief) {
            ++$this->row;
            $sheet->mergeCells("A{$this->row}:G" . ($this->row + 1));

            $this->row += 2;
            $sheet->mergeCells("A{$this->row}:G{$this->row}")
                ->setCellValue("A{$this->row}", 'Discipline Brief')
                ->cells("A{$this->row}", $this->headerStyle);

            $description = wordwrap($this->project->discipline_brief, 95);
            $rows = array_map('trim', explode("\n", $description));

            foreach ($rows as $line) {
                ++$this->row;
                $sheet->mergeCells("A{$this->row}:G{$this->row}")
                    ->getCell("A{$this->row}")
                    ->setValue($line)->getStyle()->getAlignment()->setWrapText(true);
            }
        }
    }

    /**
     * @param LaravelExcelWorksheet $sheet
     */
    protected function addAssumptions(LaravelExcelWorksheet $sheet)
    {
        if (trim($this->project->assumptions)) {
            ++$this->row;
            $sheet->mergeCells("A{$this->row}:G" . ($this->row + 1));
            $this->row += 2;
            $sheet->mergeCells("A{$this->row}:G{$this->row}")
                ->setCellValue("A{$this->row}", 'Assumptions')
                ->cells("A{$this->row}", $this->headerStyle);

            $description = wordwrap($this->project->assumptions, 95);
            $rows = array_map('trim', explode("\n", $description));

            foreach ($rows as $line) {
                ++$this->row;
                $sheet->mergeCells("A{$this->row}:G{$this->row}")
                    ->getCell("A{$this->row}")
                    ->setValue($line)->getStyle()->getAlignment()->setWrapText(true);
            }
        }
    }


}











