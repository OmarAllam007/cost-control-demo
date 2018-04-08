<?php

namespace App\Reports\Cost;

use App\MasterShadow;
use App\Period;
use App\Project;
use App\WbsLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class ActivityReport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    function __construct(Period $period)
    {
        $this->project = $period->project;
        $this->period = $period;
    }

    function run()
    {
        $project = $this->period->project;

        $tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->orderBy('name')->pluck('name', 'id');

        $activities = MasterShadow::forPeriod($this->period)->orderBy('activity')->selectRaw('DISTINCT activity')->pluck('activity');

        $period = $this->period;

        return compact('tree', 'project', 'periods', 'activities', 'period');
    }

    function buildTree()
    {
        $previousPeriod = $this->period->project->periods()->where('id', '<', $this->period->id)->orderBy('id', "DESC")->first();
        if ($previousPeriod) {
            $previousData = MasterShadow::previousActivityReport($previousPeriod)->get()->groupBy('wbs_id')->map(function ($group) {
                return $group->groupBy('activity');
            });
        } else {
            $previousData = [];
        }

        $currentData = $this->applyFilters(MasterShadow::currentActivityReport($this->period))->get()->groupBy('wbs_id')->map(function ($group) {
            return $group->groupBy('activity');
        });

        $wbsData = MasterShadow::forPeriod($this->period)->orderBy('wbs')->pluck('wbs', 'wbs_id')->map(function ($wbs) {
            return json_decode($wbs, true);
        });


        $tree = [];
        foreach ($currentData as $wbs_id => $wbsGroup) {
            foreach ($wbsGroup as $activity => $activityCurrent) {
                $key = '';
                $activityPrevious = ($previousData[$wbs_id][$activity] ?? collect())->keyBy('resource_name');

                foreach ($wbsData[$wbs_id] as $wbsLevel) {
                    $lastKey = $key;
                    $key .= $wbsLevel;
                    if (!isset($tree[$key])) {
                        $tree[$key] = [
                            'budget_cost' => 0, 'to_date_cost' => 0, 'to_date_allowable' => 0, 'to_date_var' => 0,
                            'prev_cost' => 0, 'prev_allowable' => 0, 'prev_cost_var' => 0,
                            'remaining_cost' => 0, 'completion_cost' => 0, 'completion_var' => 0, 'activities' => []
                        ];
                    }

                    $tree[$key]['parent'] = $lastKey;
                    $tree[$key]['name'] = $wbsLevel;
                    $tree[$key]['budget_cost'] += $activityCurrent->sum('budget_cost');
                    $tree[$key]['to_date_cost'] += $activityCurrent->sum('to_date_cost');
                    $tree[$key]['to_date_allowable'] += $activityCurrent->sum('to_date_allowable');
                    $tree[$key]['to_date_var'] += $activityCurrent->sum('to_date_var');
                    $tree[$key]['prev_cost'] += $activityPrevious->sum('prev_cost');
                    $tree[$key]['prev_allowable'] += $activityPrevious->sum('prev_allowable');
                    $tree[$key]['prev_cost_var'] += $activityPrevious->sum('prev_cost_var');
                    $tree[$key]['remaining_cost'] += $activityCurrent->sum('remaining_cost');
                    $tree[$key]['completion_cost'] += $activityCurrent->sum('completion_cost');
                    $tree[$key]['completion_var'] += $activityCurrent->sum('completion_var');
                }

                $activityCurrent = $activityCurrent->map(function ($resource) use ($activityPrevious) {
                    $previous = $activityPrevious->get($resource->resource_name, new Fluent());
                    $resource->prev_cost = $previous->prev_cost;
                    $resource->prev_allowable = $previous->prev_allowable;
                    $resource->prev_cost_var = $previous->prev_cost_var;
                    return $resource;
                });

                $tree[$key]['activities'][$activity] = [
                    'budget_cost' => $activityCurrent->sum('budget_cost'),
                    'to_date_cost' => $activityCurrent->sum('to_date_cost'),
                    'to_date_allowable' => $activityCurrent->sum('to_date_allowable'),
                    'to_date_var' => $activityCurrent->sum('to_date_var'),
                    'prev_cost' => $activityPrevious->sum('prev_cost'),
                    'prev_allowable' => $activityPrevious->sum('prev_allowable'),
                    'prev_cost_var' => $activityPrevious->sum('prev_cost_var'),
                    'remaining_cost' => $activityCurrent->sum('remaining_cost'),
                    'completion_cost' => $activityCurrent->sum('completion_cost'),
                    'completion_var' => $activityCurrent->sum('completion_var'),
                    'resources' => $activityCurrent
                ];
            }

        }

        return collect($tree)->filter(function ($level) {
            return $level['to_date_cost'] > 0;
        });
    }

    private function applyFilters(Builder $query)
    {
        $request = request();

        if ($status = strtolower($request->get('status', ''))) {
            if ($status == 'not started') {
                $query->havingRaw('sum(to_date_qty) = 0');
            } elseif ($status == 'in progress') {
                $query->havingRaw('sum(to_date_qty) > 0 AND AVG(progress) < 100');
            } elseif ($status == 'closed') {
                $query->where('to_date_qty', '>', 0)->where('progress', 100);
            }
        }

        if ($wbs = $request->get('wbs')) {
//            $term = "%$wbs%";
//            $levels = WbsLevel::where('project_id', $this->project->id)->where(function ($q) use ($term) {
//                $q->where('code', 'like', $term)->orWhere('name', 'like', $term);
//            })->pluck('id');
            $query->whereIn('wbs_id', $wbs);
        }

        if ($activity = $request->get('activity')) {
            $query->whereIn('activity_id', $activity);
        }

        if ($request->exists('negative_to_date')) {
            $query->havingRaw('to_date_var < 0');
        }

        if ($request->exists('negative_completion')) {
            $query->having('completion_var', '<', 0);
        }

        return $query;
    }

    public function excel()
    {
        $excel = new \PHPExcel();

        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet());
        $filename = storage_path('app/activity-' . uniqid() . '.xlsx');
        $writer = new \PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save($filename);

        $name = slug($this->project->name) . '_' . slug($this->period->name) . '_activity.xlsx';
        return \Response::download($filename, $name)->deleteFileAfterSend(true);
    }

    function sheet()
    {
        $data = $this->run();
        $tree = $data['tree'];

        $excel = \PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-activity.xlsx'));
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

        $sheet->getStyle("B{$start}:K{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');

        $sheet->getStyle("B{$start}:K{$counter}")->getNumberFormat()->setFormatCode('_(#,##0.00_);_(\(#,##0.00\);_("-"??_);_(@_)');
        $sheet->getStyle("E{$start}:E{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("G{$start}:G{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("K{$start}:K{$counter}")->setConditionalStyles([$varCondition]);

        $sheet->setShowGridlines(true);
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
                str_repeat('   ', $outlineLevel) . $level['name'],
                $level['budget_cost'] ?: '0.00',
                $level['prev_cost'] ?: '0.00',
                $level['prev_allowable'] ?: '0.00',
                $level['prev_cost_var'] ?: '0.00',
                $level['to_date_cost'] ?: '0.00',
                $level['to_date_allowable'] ?: '0.00',
                $level['to_date_var'] ?: '0.00',
                $level['remaining_cost'] ?: '0.00',
                $level['completion_cost'] ?: '0.00',
                $level['completion_var'] ?: '0.00',
            ], '', "A{$counter}");

            $sheet->getCell("A$counter")->getStyle()->applyFromArray($styleArray);
            if ($parent) {
                $sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel)->setVisible(false)->setCollapsed(true);
            }

            ++$counter;
            if ($tree->where('parent', $name)->count()) {
                $counter = $this->renderLevel($tree, $sheet, $name, $counter, $outlineLevel);
            }

            if (!empty($level['activities'])) {
                foreach ($level['activities'] as $name => $activity) {
                    $sheet->fromArray($arr = [
                        str_repeat('    ', $outlineLevel + 1) . $name,
                        $activity['budget_cost'] ?: '0.00',
                        $activity['prev_cost'] ?: '0.00',
                        $activity['prev_allowable'] ?: '0.00',
                        $activity['prev_cost_var'] ?: '0.00',
                        $activity['to_date_cost'] ?: '0.00',
                        $activity['to_date_allowable'] ?: '0.00',
                        $activity['to_date_var'] ?: '0.00',
                        $activity['remaining_cost'] ?: '0.00',
                        $activity['completion_cost'] ?: '0.00',
                        $activity['completion_var'] ?: '0.00',
                    ], '', "A{$counter}");

                    $sheet->getRowDimension($counter)->setOutlineLevel(min($outlineLevel + 1, 7))->setVisible(false)->setCollapsed(true);
                    ++$counter;

                    foreach ($activity['resources'] as $resource) {
                        $sheet->fromArray($arr = [
                            str_repeat('    ', $outlineLevel + 2) . $resource->resource_name,
                            $resource['budget_cost'] ?: '0.00',
                            $resource['prev_cost'] ?: '0.00',
                            $resource['prev_allowable'] ?: '0.00',
                            $resource['prev_cost_var'] ?: '0.00',
                            $resource['to_date_cost'] ?: '0.00',
                            $resource['to_date_allowable'] ?: '0.00',
                            $resource['to_date_var'] ?: '0.00',
                            $resource['remaining_cost'] ?: '0.00',
                            $resource['completion_cost'] ?: '0.00',
                            $resource['completion_var'] ?: '0.00',
                        ], '', "A{$counter}");

                        $sheet->getRowDimension($counter)->setOutlineLevel(min($outlineLevel + 2, 7))->setVisible(false)->setCollapsed(true);
                        ++$counter;
                    }
                }
            }
        }

        return $counter;
    }
}