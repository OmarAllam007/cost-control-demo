<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 8/14/17
 * Time: 2:54 PM
 */

namespace App\Reports\Budget;


use App\Boq;
use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Project;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class ActivityResourceBreakDownReport
{
    /** @var Collection */
    protected $tree;

    /** @var Collection */
    protected $wbs_levels;

    /** @var Project */
    protected $project;

    /** @var Collection */
    protected $boqs;

    /** @var int */
    protected $row = 1;

    function __construct(Project $project)
    {
        $this->project = $project;

        ini_set('memory_limit', max(ini_get('memory_limit'), '4G'));
    }

    function run()
    {
//            ->groupBy('wbs_id')->map(function($group) {
//            return $group->keyBy('cost_account');
//        });

        $key = 'activity-resource-breakdown-' . $this->project->id;
        $this->tree = \Cache::remember($key, 60, function () {
//            $this->wbs_levels = $this->project->wbs_levels->groupBy('parent_id');
            $this->wbs_levels = collect(\DB::table('wbs_levels')
                ->where('project_id', $this->project->id)
                ->get(['id', 'name', 'code', 'parent_id']))->groupBy('parent_id');

            $this->boqs = collect(\DB::table('boqs')
                ->where('project_id', $this->project->id)
                ->get(['id', 'description']))
                ->keyBy('id');

            return $this->buildTree();
        });

        return ['project' => $this->project, 'tree' => $this->tree];
    }

    /**
     * @param int $parent_id
     * @return Collection
     */
    protected function buildTree($parent_id = 0)
    {
        $tree = $this->wbs_levels->get($parent_id) ?: collect();

        return $tree->map(function ($level) {
            $level->activities = $this->buildActivities($level->id);

//            $level->boqs = $this->boqs->get($level->id)?: collect();

            $level->subtree = $this->buildTree($level->id);

            $level->cost = $level->subtree->sum('cost') +
                $level->activities->flatten()->sum('budget_cost');

            return $level;
        })->filter(function ($level) {
            return $level->subtree->count() || $level->activities->count();
        });
    }

    protected function buildActivities($wbs_id)
    {
        return collect(\DB::table('break_down_resource_shadows')->where('project_id', $this->project->id)
            ->where('wbs_id', $wbs_id)
            ->get(['id', 'activity', 'cost_account', 'boq_id', 'budget_cost', 'unit_price', 'budget_unit', 'resource_name', 'resource_type', 'measure_unit']))
            ->groupBy('activity')->map(function ($group) {
                return $group->groupBy('cost_account')->map(function (Collection $resources) {
                    $cost_account = collect(['resources' => $resources, 'cost' => $resources->sum('budget_cost')]);
                    $first = $resources->first();
                    $boq = $this->boqs->get($first->boq_id);
                    $cost_account->put('boq', $boq);

                    return $cost_account;
                });
            });
    }

    public function excel()
    {
        \Excel::create(slug($this->project->name) . '-activity-resource-breakdown', function (LaravelExcelWriter $excel) {
            $excel->sheet('Activity Resource Breakdown', function (LaravelExcelWorksheet $sheet) {
                $this->sheet($sheet);
            });

            $excel->download('xlsx');
        });
    }

    public function sheet(LaravelExcelWorksheet $sheet)
    {
        $this->run();

        $sheet->row($this->row, [
            'Activity', 'Resource Name', 'Resource Type', 'Price/Unit', 'Budget Unit', 'Unit of Measure', 'Budget Cost',
        ]);

        $sheet->cells("A1:G1", function (CellWriter $cells) {
            $cells->setFont(['bold' => true])->setBackground('#5182bb')->setFontColor('#ffffff');
        });

        $this->tree->each(function ($level) use ($sheet) {
            $this->buildExcel($sheet, $level);
        });

        $sheet->setColumnFormat(["D2:D{$this->row}" => '#,##0.00']);
        $sheet->setColumnFormat(["E2:E{$this->row}" => '#,##0.00']);
        $sheet->setColumnFormat(["G2:G{$this->row}" => '#,##0.00']);
//        $sheet->setAutoSize(false);
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(60);
        $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(60);
        $sheet->setAutoSize(['C', 'D', "E", 'F', 'G']);
        $sheet->setAutoSize(false);


        $sheet->setAutoFilter();
        $sheet->freezeFirstRow();
    }

    protected function buildExcel(LaravelExcelWorksheet $sheet, $level, $depth = 0)
    {
        ++$this->row;

        $sheet->mergeCells("A{$this->row}:F{$this->row}");
//        $sheet->row($this->row, [$level->name, $level->cost]);

        $sheet->setCellValue("A{$this->row}", $level->name);
        $sheet->setCellValue("G{$this->row}", $level->cost);

        $sheet->cells("A{$this->row}", function (CellWriter $cells) use ($depth) {
            $cells->setTextIndent($depth * 4)->setFont(['bold' => true]);
        });
        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel($depth < 8? $depth : 7)
                ->setCollapsed(true)->setVisible(false);
        }

        $level->subtree->each(function ($sublevel) use ($sheet, $depth) {
            $this->buildExcel($sheet, $sublevel, $depth + 1);
        });

        $level->activities->each(function($cost_accounts, $activity) use ($sheet, $depth) {
            ++$this->row;

            $sheet->setCellValue("A{$this->row}", $activity);
            $sheet->setCellValue("G{$this->row}", $cost_accounts->flatten()->sum('budget_cost'));

            $sheet->cells("A{$this->row}", function (CellWriter $cells) use ($depth) {
                $cells->setTextIndent(($depth + 1) * 4)->setFont(['bold' => true]);
            });

            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(($depth + 1) < 8 ? $depth + 1 : 7)
                ->setCollapsed(true)->setVisible(false);

            $cost_accounts->each(function ($cost_account, $label) use ($sheet, $depth) {
                ++$this->row;

                $sheet->setCellValue("A{$this->row}", $label . ' - ' .
                    ($cost_account['boq'] ? $cost_account['boq']->description : '(BOQ not found)'));

                $sheet->setCellValue("G{$this->row}", $cost_account['cost']);

                $sheet->cells("A{$this->row}", function (CellWriter $cells) use ($depth) {
                    $cells->setTextIndent(($depth + 2) * 4)->setFont(['italic' => true]);
                });

                $sheet->getRowDimension($this->row)->setOutlineLevel(($depth + 2) < 8 ? $depth + 2 : 7);

               $cost_account['resources']->each(function ($resource) use ($sheet, $depth) {
                    ++$this->row;
                    $sheet->row($this->row, [
                        '', $resource->resource_name, $resource->resource_type, $resource->unit_price, $resource->budget_unit, $resource->measure_unit, $resource->budget_cost
                    ]);

                    $sheet->getRowDimension($this->row)
                        ->setOutlineLevel(($depth + 3) < 8 ? $depth + 3 : 7)
                        ->setCollapsed(true)->setVisible(false);
                });
            });
        });
    }

}