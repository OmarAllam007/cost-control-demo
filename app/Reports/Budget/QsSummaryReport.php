<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/9/17
 * Time: 3:30 PM
 */

namespace App\Reports\Budget;


use App\Boq;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use App\StdActivity;
use App\Survey;
use App\WbsLevel;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class QsSummaryReport
{
    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $info;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Collection */
    protected $activities;

    /** @var Collection */
    protected $survies;

    /** @var Collection */
    protected $tree;

    /** @var int */
    protected $row = 1;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function run()
    {
        /** @var Collection $shadow_data */
        $shadow_data = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('DISTINCT wbs_id, activity_id, cost_account, budget_qty, eng_qty, boq_id, survey_id')
            ->get();

        $this->info = $shadow_data->groupBy('wbs_id')->map(function (Collection $group) {
            return $group->groupBy('activity_id');
        });

        $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');

        $this->activities = StdActivity::with('division')->find($shadow_data->pluck('activity_id')->toArray())->keyBy('id');
        $this->survies = Survey::with('unit')->find($shadow_data->pluck('survey_id')->toArray())->keyBy('id');

        $this->tree = $this->buildTree();


        return ['project' => $this->project, 'tree' => $this->tree];
    }

    protected function buildTree($parent = 0)
    {
        $tree = $this->wbs_levels->get($parent) ?: collect();

        return $tree->map(function (WbsLevel $level) {
            $level->subtree = $this->buildTree($level->id);

            $level->activities = collect();
            if ($this->info->has($level->id)) {
                $info = $this->info->get($level->id);

                $activity_ids = $info->keys();
                $level->activities = $this->activities->only($activity_ids->toArray())
                    ->map(function ($activity) use ($info, $level) {

                        $items = $info->get($activity->id)->map(function ($cost_account) {
                            $cost_account->boq_description = $this->survies->get($cost_account->survey_id)->description ?? '';
                            $cost_account->unit_of_measure = $this->survies->get($cost_account->survey_id)->unit->type ?? '';
                            return $cost_account;
                        });
                        return new Fluent(['id' => $activity['id'], 'name' => $activity->name, 'division' => $activity->division->name, 'items' => $items]);
                    })->groupBy('division')->sortByKeys();
            }


            return $level;
        });

//        return $tree;
    }

    function excel()
    {
        \Excel::create(slug($this->project->name) . '-qs-summary', function (LaravelExcelWriter $excel) {
            $excel->sheet('QS Summary', function (LaravelExcelWorksheet $sheet) {

                $sheet->row(1, ['Activity', 'Cost Account', 'BOQ Description', 'Eng Qty', 'Budget Qty', 'Unit of measure']);
                $sheet->cells('A1:F1', function (CellWriter $cells) {
                    $cells->setFont(['bold' => true])->setBackground('#3f6caf')->setFontColor('#ffffff');
                });

                $sheet->setAutoFilter();
                $sheet->freezeFirstRow();

                $this->tree->each(function (WbsLevel $level) use ($sheet) {
                    $this->buildExcel($sheet, $level);
                });

                $sheet->cells("A2:A{$this->row}", function (CellWriter $cells) {
                    $cells->setFont(['bold' => true]);
                });

                $sheet->setColumnFormat(["B2:B{$this->row}" => '@']);
                $sheet->setColumnFormat(["C2:C{$this->row}" => '#,##0.00']);
                $sheet->setColumnFormat(["D2:D{$this->row}" => '#,##0.00']);
            });

            $excel->download('xlsx');
        });
    }

    protected function buildExcel(LaravelExcelWorksheet $sheet, $level, $depth = 0)
    {
        $this->row++;

        $name = str_repeat(' ', $depth * 6) . $level->name;
        $sheet->mergeCells("A{$this->row}:F{$this->row}")
            ->setCellValue("A{$this->row}", $name)
            ->cells("A{$this->row}", function (CellWriter $cells) {
                $cells->setBackground('#dedede');
            });

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth < 7 ? $depth : 7)
                ->setCollapsed(true)->setVisible(false);
        }

        $level->subtree->each(function ($sublevel) use ($sheet, $depth) {
            $this->buildExcel($sheet, $sublevel, $depth + 1);
        });

        $level->activities->each(function ($group, $division_name) use ($sheet, $depth) {
            $this->row++;
            $newDepth = $depth + 1;
            $name = str_repeat(' ', $newDepth * 6) . $division_name;
            $sheet->mergeCells("A{$this->row}:F{$this->row}")
                ->setCellValue("A{$this->row}", $name)
                ->cells("A{$this->row}", function (CellWriter $cells) {
                    $cells->setBackground('#ededed');
                });

            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($newDepth < 7 ? $newDepth : 7)
                ->setCollapsed(true)->setVisible(false);

            $group->each(function ($activity) use ($sheet, $depth) {
                ++$this->row;

                $newDepth = $depth + 2;
                $name = $name = str_repeat(' ', $newDepth * 6) . $activity->name;
                $sheet->mergeCells("A{$this->row}:F{$this->row}")
                    ->setCellValue("A{$this->row}", $name)
                    ->cells("A{$this->row}", function (CellWriter $cells) {
                        $cells->setBackground('#f7f7f7');
                    })->getRowDimension($this->row)
                    ->setOutlineLevel($newDepth < 7 ? $newDepth : 7)
                    ->setCollapsed(true)->setVisible(false);

                $activity->cost_accounts->each(function ($cost_account) use ($sheet, $newDepth) {
                    ++$this->row;
                    $sheet->row($this->row, [
                        '', $cost_account->cost_account, $cost_account->boq_description,
                        $cost_account->eng_qty, $cost_account->budget_qty, $cost_account->unit_of_measure
                    ])->getRowDimension($this->row)
                        ->setOutlineLevel(($newDepth + 1) < 7 ? $newDepth + 1 : 7)
                        ->setCollapsed(true)->setVisible(false);;
                });
            });
        });

    }
}