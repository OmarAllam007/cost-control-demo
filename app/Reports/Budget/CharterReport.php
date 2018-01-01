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
        $this->total = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');

        $this->resource_types = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('resource_type as type, sum(budget_cost) as budget_cost')
            ->groupBy('resource_type')->orderBy('resource_type')
            ->get()->map(function ($type) {
                $type->weight = $type->budget_cost * 100 / $this->total;
                return $type;
            });

        $this->disciplines = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('a.discipline as discipline, sum(budget_cost) as budget_cost')
            ->join('std_activities as a', 'activity_id', '=', 'a.id')
            ->groupBy('a.discipline')->orderBy('a.discipline')
            ->get()->map(function ($discipline) {
                $discipline->weight = $discipline->budget_cost * 100 / $this->total;
                return $discipline;
            });

        $this->project->general_requirements = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->where('resource_type_id', 1)
            ->selectRaw('sum(budget_cost) as cost')->value('cost');

        $this->project->management_reserve = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->where('resource_type_id', 8)
            ->selectRaw('sum(budget_cost) as cost')->value('cost');

        $this->project->direct_cost = $this->total - $this->project->general_requirements - $this->project->management_reserve;

        $this->project->profit = $this->total - $this->project->project_contract_signed_value -$this->project->change_order_amount;

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
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->setBorder("A1:G{$this->row}", 'thin');
        $sheet->setAutoFilter(true);
    }

    protected function addBasicInfo(LaravelExcelWorksheet $sheet)
    {
        $sheet->cells('A1', $this->headerStyle);

        $sheet->setCellValue('A1', 'Project Basic Info');

        foreach (range(2, 19) as $i) {
            $sheet->mergeCells("A{$i}:B{$i}");
            $sheet->mergeCells("C{$i}:G{$i}");
        }

        //<editor-fold defaultstate="collapsed" desc="Project basic info">
        $sheet->setCellValue('A2', 'Project Name');
        $sheet->setCellValue('C2', $this->project->name);
        $sheet->setCellValue('A3', 'Project Client');
        $sheet->setCellValue('C3', $this->project->client_name);
        $sheet->setCellValue('A4', 'Project Consultant');
        $sheet->setCellValue('C4', $this->project->consultant);
        $sheet->setCellValue('A5', 'Project Location');
        $sheet->setCellValue('C5', $this->project->project_location);
        $sheet->setCellValue('A6', 'Project Type');
        $sheet->setCellValue('C6', $this->project->project_type);
        $sheet->setCellValue('A7', 'Project Duration');
        $sheet->setCellValue('C7', $this->project->project_duration);
        $sheet->setCellValue('A8', 'Project Plan Start Sate');
        $sheet->setCellValue('C8', $this->project->project_start_date);
        $sheet->setCellValue('A9', 'Project Plan Finish Date');
        $sheet->setCellValue('C9', $this->project->expected_finished_date);
        $sheet->setCellValue('A10', 'Contract Type');
        $sheet->setCellValue('C10', $this->project->contract_type);
        $sheet->setCellValue('A11', 'Project Selling Cost');
        $sheet->setCellValue('C11', $this->project->project_contract_signed_value);
        $sheet->setCellValue('A12', 'Total Project Dry Cost');
        $sheet->setCellValue('C12', $this->project->dry_cost);
        $sheet->setCellValue('A13', 'Project Overhead + GR');
        $sheet->setCellValue('C13', $this->project->overhead_and_gr);
        $sheet->setCellValue('A14', 'Project Estimated Profit + Risk');
        $sheet->setCellValue('C14', $this->project->estimated_profit_and_risk);
        $sheet->setCellValue('A15', 'Project Total Budget');
        $sheet->setCellValue('C15', $this->total);
        $sheet->setCellValue('A16', 'Project Direct Cost Budget');
        $sheet->setCellValue('C16', $this->project->direct_cost);
        $sheet->setCellValue('A17', 'Project General Requirement Budget');
        $sheet->setCellValue('C17', $this->project->general_requirements);
        $sheet->setCellValue('A18', 'Management Reserve');
        $sheet->setCellValue('C18', $this->project->management_reserve);
        $sheet->setCellValue('A19', 'Project Estimated Profit After Budget');
        $sheet->setCellValue('C19', $this->project->profit);
        //</editor-fold>

        $sheet->cells('A1:A19', function ($cells) {
            $cells->setFont(['bold' => true]);
        })->setColumnFormat([
            'C11:C19' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ])->cells("A15:G19", function (CellWriter $cells) {
            $cells->setBackground('#DDF4FF');
        });

        $sheet->mergeCells("A20:G21");
    }

    /**
     * @param LaravelExcelWorksheet $sheet
     */
    protected function addDescription(LaravelExcelWorksheet $sheet)
    {
        $this->row = 23;
        $sheet->mergeCells('A22:G22')
            ->setCellValue('A22', 'Project Brief')
            ->cells("A22", $this->headerStyle);

        $description = wordwrap($this->project->description, 95);
        $rows = array_map('trim', explode("\n", $description));

        foreach ($rows as $line) {
            $sheet->mergeCells("A{$this->row}:G{$this->row}")
                ->getCell("A{$this->row}")
                ->setValue($line)->getStyle()->getAlignment()->setWrapText(true);
            ++$this->row;
        }

        $sheet->mergeCells("A{$this->row}:G" . ($this->row + 1));
        $this->row += 2;
    }

    protected function addBudgetByDiscipline(LaravelExcelWorksheet $sheet)
    {
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

        ++$this->row;
        $sheet->mergeCells("A{$this->row}:G" . ($this->row + 1));
        $this->row += 2;
    }

    /**
     * @param LaravelExcelWorksheet $sheet
     */
    protected function addDisciplineBrief(LaravelExcelWorksheet $sheet)
    {
        if ($this->project->discipline_brief) {
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

            ++$this->row;
            $sheet->mergeCells("A{$this->row}:G" . ($this->row + 1));
            $this->row += 2;
        }
    }

    /**
     * @param LaravelExcelWorksheet $sheet
     */
    protected function addAssumptions(LaravelExcelWorksheet $sheet)
    {
        if (trim($this->project->assumptions)) {
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











