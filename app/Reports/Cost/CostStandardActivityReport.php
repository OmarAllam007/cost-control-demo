<?php

namespace App\Reports\Cost;

use App\ActivityDivision;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\StdActivity;
use Illuminate\Support\Collection;

class CostStandardActivityReport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    /** @var Period */
    protected $previousPeriod;

    /** @var Collection */
    protected $activityNames;

    function __construct(Period $period)
    {
        $this->project = $period->project;
        $this->period = $period;
    }

    function run()
    {
        $this->previousPeriod = $this->project->periods()->where('id', '<', $this->period->id)->orderBy('id')->first();
        if ($this->previousPeriod) {
            $previousTotals = MasterShadow::whereProjectId($this->project->id)->wherePeriodId($this->previousPeriod->id)
                ->selectRaw('sum(to_date_cost) previous_cost, sum(allowable_ev_cost) previous_allowable, sum(allowable_var) as previous_var')
                ->first();
        } else {
            $previousTotals = ['previous_cost' => 0, 'previous_allowable' => 0, 'previous_var' => 0];
        }

        $currentTotals = MasterShadow::whereProjectId($this->project->id)->wherePeriodId($this->period->id)->selectRaw(
            'sum(to_date_cost) to_date_cost, sum(allowable_ev_cost) to_date_allowable, sum(allowable_var) as to_date_var,'
            . 'sum(remaining_cost) as remaining, sum(completion_cost) at_completion_cost, sum(cost_var) cost_var, sum(budget_cost) budget_cost'
        )->first();

        $tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->pluck('name', 'id');
        $activityNames = $this->activityNames;
        $divisionNames = ActivityDivision::parents()->orderBy('code')->orderBy('name')->get(['id', 'code', 'name'])
            ->keyBy('id')->map(function (ActivityDivision $div) {
                return $div->code . ' ' . $div->name;
            });

        $project = $this->project; $period = $this->period;
        return compact('project', 'period', 'currentTotals', 'previousTotals', 'tree', 'periods', 'activityNames', 'divisionNames');
    }

    protected function buildTree()
    {
        $query = \DB::table('master_shadows')->whereProjectId($this->project->id)->wherePeriodId($this->period->id)->selectRaw(
            'activity_id, activity, sum(to_date_cost) to_date_cost, sum(allowable_ev_cost) to_date_allowable, sum(allowable_var) as to_date_var,'
            . 'sum(remaining_cost) as remaining_cost, sum(completion_cost) completion_cost, sum(cost_var) completion_var, sum(budget_cost) budget_cost'
        );

        $this->applyFilters($query);

        $currentActivities = collect($query->groupBy('activity', 'activity_id')->orderBy('activity')->get())->keyBy('activity_id');
        $activity_ids = $currentActivities->pluck('activity_id');
        $this->activityNames = $currentActivities->pluck('activity', 'activity_id')->sort();

        if ($this->previousPeriod) {
            $previousActivities = collect(\DB::table('master_shadows')->whereProjectId($this->project->id)->wherePeriodId($this->previousPeriod->id)->selectRaw(
                'activity_id, activity, sum(to_date_cost) previous_cost, sum(allowable_ev_cost) previous_allowable, sum(allowable_var) as previous_var'
            )->whereIn('activity_id', $activity_ids)->groupBy('activity', 'activity_id')->orderBy('activity')->get())->keyBy('activity_id');
        } else {
            $previousActivities = [];
        }

        $activityDivs = collect(\DB::table('master_shadows')->whereProjectId($this->project->id)
            ->wherePeriodId($this->period->id)->whereIn('activity_id', $activity_ids)
            ->pluck('activity_divs', 'activity_id'))->map(function ($div) {
            return json_decode($div, true);
        });

        $tree = [];

        foreach ($currentActivities as $id => $current) {
            $prevDiv = '';
            $previous = $previousActivities[$id] ?? [];
            foreach ($activityDivs[$id] as $index => $div) {
                if (!isset($tree[$div])) {
                    $tree[$div] = [
                        'budget_cost' => 0, 'to_date_cost' => 0, 'to_date_allowable' => 0, 'to_date_var' => 0,
                        'previous_cost' => 0, 'previous_allowable' => 0, 'previous_var' => 0,
                        'remaining_cost' => 0, 'completion_cost' => 0, 'completion_var' => 0
                    ];
                }

                $tree[$div]['index'] = $index;
                $tree[$div]['parent'] = $prevDiv;
                $tree[$div]['budget_cost'] += $current->budget_cost;
                $tree[$div]['to_date_cost'] += $current->to_date_cost;
                $tree[$div]['to_date_allowable'] += $current->to_date_allowable;
                $tree[$div]['to_date_var'] += $current->to_date_var;
                $tree[$div]['remaining_cost'] += $current->remaining_cost;
                $tree[$div]['completion_cost'] += $current->completion_cost;
                $tree[$div]['completion_var'] += $current->completion_var;
                $tree[$div]['previous_cost'] += $previous->previous_cost ?? 0;
                $tree[$div]['previous_allowable'] += $previous->previous_allowable ?? 0;
                $tree[$div]['previous_var'] += $previous->previous_var ?? 0;

                $prevDiv = $div;
            }

            $tree[$prevDiv]['activities'][] = [
                'name' => $current->activity,
                'budget_cost' => $current->budget_cost,
                'to_date_cost' => $current->to_date_cost,
                'to_date_allowable' => $current->to_date_allowable,
                'to_date_var' => $current->to_date_var,
                'remaining_cost' => $current->remaining_cost,
                'completion_cost' => $current->completion_cost,
                'completion_var' => $current->completion_var,
                'previous_cost' => $previous->previous_cost ?? 0,
                'previous_allowable' => $previous->previous_allowable ?? 0,
                'previous_var' => $previous->previous_var ?? 0,
            ];

        }

        return collect($tree)->sortByKeys();
    }

    protected function applyFilters($query)
    {
        $request = request();

        if ($activity_id = $request->get('activity')) {
            $query->whereIn('activity_id', $activity_id);
        }

        if ($status = strtolower($request->get('status', ''))) {
            if ($status == 'not started') {
                $query->havingRaw('sum(to_date_qty) = 0');
            } elseif ($status == 'in progress') {
                $query->havingRaw('sum(to_date_qty) > 0 AND AVG(progress) < 100');
            } elseif ($status == 'closed') {
                $query->where('to_date_qty', '>', 0)->where('progress', 100);
            }
        }

        if ($div_id = $request->get('div')) {
            $div = ActivityDivision::find($div_id);
            if ($div) {
                $activity_ids = StdActivity::whereIn('division_id', $div->getChildrenIds())->pluck('id');
                $query->whereIn('activity_id', $activity_ids);
            }
        }

        if ($request->exists('negative')) {
            $query->having('to_date_var', '<', 0);
        }

    }

    function excel()
    {
        $excel = new \PHPExcel();

        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet());
        $filename = storage_path('app/std_activity-' . uniqid() . '.xlsx');
        $writer = new \PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save($filename);

        $name = slug($this->project->name) . '_' . slug($this->period->name) . '_standard_activity.xlsx';
        return \Response::download($filename, $name)->deleteFileAfterSend(true);
    }

    function sheet()
    {
        $data = $this->run();
        $tree = $data['tree'];
        $currentTotals = $data['currentTotals'];
        $previousTotals = $data['previousTotals'];

//        extract($data);

        $excel = \PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-std-activity.xlsx'));
        $sheet = $excel->getActiveSheet();

        $varCondition = new \PHPExcel_Style_Conditional();
        $varCondition->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS);
        $varCondition->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_LESSTHAN);
        $varCondition->addCondition(0);
        $varCondition->getStyle()->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_RED);

        $projectCell = $sheet->getCell('A4');
        $issueDateCell = $sheet->getCell('A5');
        $periodCell = $sheet->getCell('A6');

        $projectCell->setValue($projectCell->getValue() . ' ' . $this->project->name);
        $issueDateCell->setValue($issueDateCell->getValue() . ' ' . date('d M Y'));
        $periodCell->setValue($periodCell->getValue() . ' ' . $this->period->name);

        $logo = imagecreatefrompng(public_path('images/kcc.png'));
        $drawing = new \PHPExcel_Worksheet_MemoryDrawing();
        $drawing->setName('Logo')->setImageResource($logo)
            ->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
            ->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
            ->setCoordinates('J2')->setWorksheet($sheet);

        $start = 11;
        $counter = $start;

        $counter = $this->renderLevel($tree, $sheet, '', $counter);

        $sheet->fromArray([
            "Totals", $currentTotals['budget_cost'] ?: 0, $previousTotals['previous_cost'] ?: 0, $previousTotals['previous_allowable'] ?: 0,
            $previousTotals['previous_var'] ?: 0, $currentTotals['to_date_cost'] ?: 0, $currentTotals['to_date_allowable'] ?: 0, $currentTotals['to_date_var'] ?: 0,
            $currentTotals['remaining'] ?: 0, $currentTotals['at_completion_cost'] ?: 0, $currentTotals->cost_var ?: 0,
        ], '', "A{$counter}", false);

        $totalsStyles = $sheet->getStyle("A{$counter}:Y{$counter}");
        $totalsStyles->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new \PHPExcel_Style_Color('DAEEF3'));
        $totalsStyles->getFont()->setBold(true);

        $sheet->getStyle("B{$start}:Y{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
        $sheet->getStyle("E{$start}:E{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("H{$start}:H{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("K{$start}:K{$counter}")->setConditionalStyles([$varCondition]);

        $sheet->setShowSummaryBelow(false);

        return $sheet;
    }

    private function renderLevel($tree, \PHPExcel_Worksheet $sheet, $parent, $counter, $outlineLevel = 0)
    {
        $styleArray = ['font' => ['bold' => true]];

        if ($parent) {
            ++$outlineLevel;
            if ($outlineLevel >= 7) {
                $outlineLevel = 7;
            }
        }

        foreach ($tree->where('parent', $parent) as $name => $level) {
            $sheet->fromArray([
                str_repeat("    ", $outlineLevel) . $name, $level['budget_cost'] ?: 0, $level['previous_cost'] ?: 0, $level['previous_allowable'] ?: 0, $level['previous_var'] ?: 0,
                $level['to_date_cost'] ?: 0, $level['to_date_allowable'] ?: 0, $level['to_date_var'] ?: 0, $level['remaining_cost'] ?: 0,
                $level['completion_cost'] ?: 0, $level['completion_var'] ?: 0,
            ], 0, "A{$counter}", false);

            $sheet->getCell("A$counter")->getStyle()->applyFromArray($styleArray);
            if ($parent) {
                $sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel)->setVisible(false)->setCollapsed(true);
            }



            ++$counter;
            if ($tree->where('parent', $name)->count()) {
                $counter = $this->renderLevel($tree, $sheet, $name, $counter, $outlineLevel);
            }

            if (!empty($level['activities'])) {
                foreach ($level['activities'] as $activity) {
                    $sheet->fromArray($arr = [
                        str_repeat("    ", $outlineLevel + 1) . $activity['name'], $activity['budget_cost'] ?: 0, $activity['previous_cost'] ?: 0, $activity['previous_allowable'] ?: 0,
                        $activity['previous_var'] ?: 0, $activity['to_date_cost'] ?: 0, $activity['to_date_allowable'] ?: 0, $activity['to_date_var'] ?: 0,
                        $activity['remaining_cost'] ?: 0, $activity['completion_cost'] ?: 0, $activity['completion_var'] ?: 0,
                    ], '', "A{$counter}", false);

                    $sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel + 1)->setVisible(false)->setCollapsed(true);
                    ++$counter;
                }
            }
        }

        return $counter;
    }

}