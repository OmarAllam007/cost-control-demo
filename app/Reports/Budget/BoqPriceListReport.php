<?php

namespace App\Reports\Budget;


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
    protected $cost_accounts;

    /** @var int */
    protected $row = 1;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    function run()
    {

        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $raw_cost_accounts = BreakDownResourceShadow::where('project_id', $this->project->id)
            ->selectRaw('wbs_id, survey_id, resource_type, sum(budget_cost) as budget_cost')
            ->groupBy(['wbs_id', 'survey_id', 'resource_type'])->get();
        $time = microtime(1);

        $this->surveys = Survey::with('unit')
            ->find($raw_cost_accounts->pluck('survey_id')->unique()->toArray())
            ->keyBy('id');

        $this->cost_accounts = $raw_cost_accounts->groupBy('wbs_id')->map(function (Collection $surveys) {
            return $surveys->groupBy('survey_id')->map(function (Collection $resource_types, $survey_id) {
                $survey = $this->surveys->get($survey_id);
                return collect([
                    'description' => $survey->description ?? '',
                    'unit_of_measure' => $survey->unit->type ?? '',
                    'budget_qty' => $survey->budget_qty ?? 0,
                    'cost_account' => $survey->cost_account ?? '',
                    'types' => $resource_types->map(function ($type) {
                        $type->resource_type = strtolower($type->resource_type);
                        return $type;
                    })->pluck('budget_cost', 'resource_type'),
                    'grand_total' => $resource_types->sum('budget_cost'),
                ]);
            });
        });

        $this->tree = $this->buildTree();

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    /**
     * @param int $parent_id
     * @return Collection
     */
    protected function buildTree($parent_id = 0)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        $tree->map(function (WbsLevel $level) {
            $level->subtree = $this->buildTree($level->id);

            $level->cost_accounts = $this->cost_accounts->get($level->id) ?: collect();

            $level->cost = $level->cost_accounts->sum('grand_total') + $level->subtree->sum('cost');

            return $level;
        });

        return $tree;
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '_boq-price-list.xlsx', function (LaravelExcelWriter $excel) {
            $excel->sheet('BOQ Price List', function (LaravelExcelWorksheet $sheet) {
                $sheet->row(1, [
                    'Item', 'Cost Account', 'Budget Qty', 'U.O.M', 'General Requirement', 'Labours', 'Material',
                    'Subcontractors', 'Equipment', 'Scaffolding', 'Others', 'Grand Total'
                ]);

                $this->tree->each(function (WbsLevel $level) use ($sheet) {
                    $this->buildExcel($sheet, $level);
                });

                $sheet->setAutoFilter();
                $sheet->setColumnFormat(["B2:B{$this->row}" => '@']);
                $sheet->setColumnFormat(["C2:C{$this->row}" => '#,##0.00']);
                $sheet->setColumnFormat(["E2:L{$this->row}" => '#,##0.00']);
            });

            $excel->download('xlsx');
        });
    }

    protected function buildExcel(LaravelExcelWorksheet $sheet, WbsLevel $level, $depth = 0)
    {
        ++$this->row;
        $sheet->mergeCells("A{$this->row}:K{$this->row}");
        $name = (str_repeat(' ', 6 * $depth)) . $level->name;
        $sheet->row($this->row, [$name, $level->cost]);

        $sheet->cells("A{$this->row}:L{$this->row}", function (CellWriter $cells) {
            $cells->setBackground('#f7f7f7')->setFont(['bold' => true]);
        });

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth < 8 ? $depth : 8)
                ->setCollapsed(true)->setVisible(false);
        }


        $level->subtree->each(function (WbsLevel $level) use ($sheet, $depth) {
            $this->buildExcel($sheet, $level, $depth + 1);
        });

        $level->cost_accounts->sortBy('description')->each(function ($cost_account) use ($sheet, $depth) {
            ++$this->row;
            $description = str_repeat(' ', 6 * ($depth + 1)) . $cost_account['description'];
            $sheet->row($this->row, [
                $description, $cost_account['cost_account'], $cost_account['budget_qty'], $cost_account['unit_of_measure'],
                $cost_account['types']['01.general requirment'] ?? 0, $cost_account['types']['02.labors'] ?? 0,
                $cost_account['types']['03.material'] ?? 0, $cost_account['types']['04.subcontractors'] ?? 0,
                $cost_account['types']['05.equipment'] ?? 0, $cost_account['types']['06.scaffolding'] ?? 0,
                $cost_account['types']['07.others'] ?? 0,
                $cost_account['grand_total'],
            ]);

            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth + 1 < 8? $depth + 1 : 8)
                ->setCollapsed(true)->setVisible(false);
        });
    }
}