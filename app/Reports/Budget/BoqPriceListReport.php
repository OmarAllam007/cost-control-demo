<?php

namespace App\Reports\Budget;


use App\Boq;
use App\BreakDownResourceShadow;
use App\Project;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class BoqPriceListReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $boqs;

    /** @var Collection */
    protected $cost_accounts;

    /** @var int */
    protected $row = 1;
    /** @var Collection */
    private $qty_surveys;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {

        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $raw_cost_accounts = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('wbs_id, boq_id, survey_id, cost_account, resource_type, budget_qty, sum(boq_equivilant_rate) as budget_cost')
            ->groupBy(['wbs_id', 'boq_id', 'survey_id', 'cost_account', 'resource_type', 'budget_qty'])->get();

        $this->qty_surveys = Survey::with('unit')->where('project_id', $this->project->id)->get()->keyBy('id');

        $this->boqs = Boq::with('unit')
            ->find($raw_cost_accounts->pluck('boq_id')->unique()->toArray())
            ->keyBy('id');

        $this->cost_accounts = $raw_cost_accounts->groupBy('wbs_id')->map(function (Collection $boqs) {
            return $boqs->groupBy('boq_id')->map(function ($surveys, $boq_id) {
                $cost_accounts = $surveys->groupBy('survey_id')->map(function (Collection $resource_types, $survey_id) {
                    $qs = $this->qty_surveys->get($survey_id);
                    $first = $resource_types->first();
                    return collect([
                        'description' => $qs->description ?? '',
                        'unit_of_measure' => $qs->unit->type ?? '',
                        'budget_qty' => $first->budget_qty ?? 0,
                        'cost_account' => $first->cost_account,
                        'types' => $resource_types->map(function ($type) {
                            $type->resource_type = strtolower($type->resource_type);
                            return $type;
                        })->pluck('budget_cost', 'resource_type'),
                        'grand_total' => $resource_types->sum('budget_cost'),
                    ]);
                });

                $boq = $this->boqs->get($boq_id);
                return [
                    'description' => $boq->description ?? '', 'cost_account' => $boq->cost_account ?? '',
                    'surveys' => $cost_accounts, 'id' => $boq_id,
                ];
            });
        });

        $this->tree = $this->buildTree();
//        dd($this->project);

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    /**
     * @param int $parent_id
     * @return Collection
     */
    protected function buildTree($parent_id = 0)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        return $tree->map(function (WbsLevel $level) {
            $level->subtree = $this->buildTree($level->id);

            $level->cost_accounts = $this->cost_accounts->get($level->id) ?: collect();

            $level->cost = $level->cost_accounts->sum('grand_total') + $level->subtree->sum('cost');

            return $level;
        })->filter(function ($level) {
            return $level->cost_accounts->count() || $level->subtree->count();
        });
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '_boq-price-list.xlsx', function (LaravelExcelWriter $excel) {
            $excel->sheet('BOQ Price List', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $excel->download('xlsx');
        });
    }

    protected function buildExcel(LaravelExcelWorksheet $sheet, WbsLevel $level, $depth = 0)
    {
        ++$this->row;
        $sheet->mergeCells("A{$this->row}:K{$this->row}");
        $name = $level->name;
        $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent(4 * $depth);
        $sheet->row($this->row, [$name, $level->cost]);

        $sheet->cells("A{$this->row}:L{$this->row}", function (CellWriter $cells) {
            $cells->setBackground('#BCDEFA')->setFont(['bold' => true]);
        });

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setCollapsed(true)->setVisible(false);
        }

        ++$depth;

        $level->subtree->each(function (WbsLevel $level) use ($sheet, $depth) {
            $this->buildExcel($sheet, $level, $depth);
        });

        $level->cost_accounts->sortBy('description')->each(function ($boq) use ($sheet, $depth) {
            ++$this->row;
            $sheet->row($this->row, [$boq['description'], $boq['cost_account']]);
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth + 1, 7))
                ->setCollapsed(true)->setVisible(false);

            $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent(4 * ($depth + 1));
            $sheet->getStyle("A{$this->row}")->getFont()->setBold(true);

            foreach ($boq['surveys'] as $cost_account) {
                ++$this->row;
                $sheet->row($this->row, [
                    $cost_account['description'], $cost_account['cost_account'], $cost_account['budget_qty'], $cost_account['unit_of_measure'],
                    $cost_account['types']['01.general requirment'] ?? 0, $cost_account['types']['02.labors'] ?? 0,
                    $cost_account['types']['03.material'] ?? 0, $cost_account['types']['04.subcontractors'] ?? 0,
                    $cost_account['types']['05.equipment'] ?? 0, $cost_account['types']['06.scaffolding'] ?? 0,
                    $cost_account['types']['07.others'] ?? 0,
                    $cost_account['grand_total'],
                ]);
                $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent(4 * ($depth + 2));


                $sheet->getRowDimension($this->row)
                    ->setOutlineLevel(min($depth + 2, 7))
                    ->setCollapsed(true)->setVisible(false);
            }

        });
    }

    public function sheet($sheet)
    {
        /** @var LaravelExcelWorksheet $sheet */
        $this->run();

        $sheet->row(1, [
            'Item', 'Cost Account', 'Budget Qty', 'U.O.M', 'General Requirement', 'Labours', 'Material',
            'Subcontractors', 'Equipment', 'Scaffolding', 'Others', 'Grand Total'
        ]);

        $sheet->getStyle("A1:L1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'EFF8FF']],
            'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => '2779BD']]
        ]);

        $this->tree->each(function (WbsLevel $level) use ($sheet) {
            $this->buildExcel($sheet, $level);
        });

        $sheet->setAutoSize(false);
        $sheet->getColumnDimension('A')->setWidth(90)->setAutoSize(false);
        foreach (range('B', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->setColumnFormat(["B2:B{$this->row}" => '@']);
        $sheet->setColumnFormat(["C2:C{$this->row}" => '#,##0.00']);
        $sheet->setColumnFormat(["E2:L{$this->row}" => '#,##0.00']);

        $sheet->setShowSummaryBelow(false);
        $sheet->setSelectedCell("A2");
    }
}