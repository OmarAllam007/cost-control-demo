<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 26/12/16
 * Time: 11:51 ص
 */

namespace App\Http\Controllers\Reports\CostReports;


use App\BreakDownResourceShadow;
use App\BusinessPartner;
use App\CostShadow;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Resources;
use App\ResourceType;
use Illuminate\Database\Eloquent\Builder;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;

class ResourceCodeReport
{
    /** @var Project */
    protected $project;
    /** @var Period */
    protected $period;

    function __construct($period)
    {
        $this->project = $period->project;
        $this->period = $period;
    }

    public function run()
    {
        $tree = $this->buildTree();
        $project = $this->project;
        $period = $this->period;

        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        $types = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('DISTINCT resource_type')->orderBy('resource_type')->pluck('resource_type');

        $disciplines = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT boq_discipline')->orderBy('boq_discipline')->pluck('boq_discipline');

        $topMaterials = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT top_material')->orderBy('top_material')->pluck('top_material')->filter();

        return compact('project', 'tree', 'periods', 'types', 'disciplines', 'topMaterials', 'period');
    }

    private function buildTree()
    {
        $query = MasterShadow::forPeriod($this->period)->resourceDictReport();

        $resourceData = $this->applyFilters($query)->get()->keyBy('resource_id');
        $resourceData->where('resource_type_id', 8)->each(function ($resource) {
            $budget_cost = MasterShadow::where('period_id', $this->period->id)->where('activity_id', '<>', 3060)->sum('budget_cost');
            $to_date_cost = MasterShadow::where('period_id', $this->period->id)->where('activity_id', '<>', 3060)->sum('to_date_cost');
            $progress = min(100, $to_date_cost / $budget_cost);
            $reserve = $resource->budget_cost;
            $to_date_allowable = $progress * $reserve;

            $resource->budget_cost = $reserve;
            $resource->to_date_cost = 0;
            $resource->to_date_allowable = $to_date_allowable;
            $resource->to_date_var = $to_date_allowable;
            $resource->prev_cost = 0;
            $resource->prev_allowable = 0;
            $resource->prev_cost_var = 0;
            $resource->remaining_cost = 0;
            $resource->at_completion_cost = 0;
            $resource->cost_var = $reserve;
        });

        $tree = $resourceData->groupBy('resource_type')->map(function ($typeGroup) {
            return $typeGroup->groupBy('boq_discipline')->map(function ($disciplineGroup) {
                return $disciplineGroup->groupBy('top_material');
            });
        });

        return $tree;
    }

    protected function applyFilters(Builder $query)
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

        // We are doing like here because data is not clean and some types are repeated with spaces
        // After data cleaning, where this still valid, we can safely rely on resource_type_id
        if ($type = $request->get('type')) {
            // rt is the alias for joined resource type table
            $query->where('rt.name', 'like', "%$type%");
        }

        if ($top = $request->get('top')) {
            // We have to consider that resources without discipline are mapped to general also
            if (strtolower($top) == 'all') {
                $query->whereNotNull('top_material')->where('top_material', '!=', '');
            } elseif (strtolower($top) == 'other') {
                $query->where(function ($q) {
                    $q->whereNull('top_material')->orWhere('top_material', '');
                });
            } else {
                $query->where('top_material', $top);
            }
        }

        if ($discipline = $request->get('discipline')) {
            // We have to consider that resources without discipline are mapped to general also
            if (strtolower($discipline) == 'general') {
                $query->where(function ($q) {
                    $q->where('boq_discipline', 'general')->orWhere('boq_discipline', '')->orWhereNull('boq_discipline');
                });
            } else {
                $query->where('boq_discipline', $discipline);
            }
        }

        if ($resource = $request->get('resource')) {
            $query->where(function ($q) use ($resource) {
                $term = "%$resource%";
                $q->where('resource_code', 'like', $term)->orWhere('resource_name', 'like', $term);
            });
        }

        if ($request->exists('negative_to_date')) {
            $query->havingRaw('to_date_allowable - to_date_cost < 0');
        }

        if ($request->exists('negative_completion')) {
            $query->having('cost_var', '<', 0);
        }

        return $query;
    }

    function excel()
    {
        $excel = new \PHPExcel();
        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet(), 0);

        $filename = storage_path('app/' . uniqid('cost_resource_dict_') . '.xlsx');
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);

        return \Response::download($filename,
            slug($this->project->name) . '_' . slug($this->period->name) . '_resource_dict.xlsx'
        )->deleteFileAfterSend(true);
    }

    function sheet()
    {
        $data = $this->run();
        $tree = $data['tree'];
        $project = $data['project'];
        $period = $data['period'];

        $excel = \PHPExcel_IOFactory::createReader('Excel2007')->load(storage_path('templates/cost-resource-dict.xlsx'));
        $sheet = $excel->getActiveSheet();

        $varCondition = new \PHPExcel_Style_Conditional();
        $varCondition->setConditionType(\PHPExcel_Style_Conditional::CONDITION_CELLIS);
        $varCondition->setOperatorType(\PHPExcel_Style_Conditional::OPERATOR_LESSTHAN);
        $varCondition->addCondition(0);
        $varCondition->getStyle()->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_RED);

        $projectCell = $sheet->getCell('A4');
        $issueDateCell = $sheet->getCell('A5');
        $periodCell = $sheet->getCell('A6');

        $projectCell->setValue($projectCell->getValue() . ' ' . $project->name);
        $issueDateCell->setValue($issueDateCell->getValue() . ' ' . date('d M Y'));
        $periodCell->setValue($periodCell->getValue() . ' ' . $period->name);

        $logo = imagecreatefrompng(public_path('images/kcc.png'));
        $drawing = new \PHPExcel_Worksheet_MemoryDrawing();
        $drawing->setName('Logo')->setImageResource($logo)
            ->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
            ->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
            ->setCoordinates('X2')->setWorksheet($sheet);

        $start = 12;
        $counter = $start;

        $this->bold = ['font' => ['bold' => true]];

        $accent1 = [
            'fill' => [
                'type' => 'solid', 'startColor' => dechex(131) . dechex(163) . dechex(206)
            ],
            'font' => ['color' => 'ffffff']
        ];

        foreach ($tree as $name => $typeData) {
            ++$counter;
            $counter = $this->addExcelType($sheet, $name, $typeData, $counter);
        }

        $sheet->getStyle("B{$start}:Z{$counter}")->getNumberFormat()->setBuiltInFormatCode(40);

        $totalsStyles = $sheet->getStyle("A{$counter}:Z{$counter}");
        $totalsStyles->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('DAEEF3'));
        $totalsStyles->getFont()->setBold(true);

//        $sheet->getStyle("N{$start}:N{$counter}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("Q{$start}:Q{$counter}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("W{$start}:W{$counter}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("Y{$start}:Y{$counter}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("Z{$start}:Z{$counter}")->setConditionalStyles([$varCondition]);

        $sheet->setShowGridlines(false);

        $sheet->setShowSummaryBelow(false);

        $sheet->setTitle('Resource Dict (Cost)');

        return $sheet;
    }

    private function addExcelType($sheet, $name, $typeData, $counter)
    {
        $totals = $typeData->reduce(function ($totals, $disciplineData) {
            return $disciplineData->reduce(function ($totals, $topMaterialData) {
                return $topMaterialData->reduce(function ($totals, $row) {
                    $totals['budget_cost'] += $row->budget_cost;
                    $totals['prev_cost'] += $row->prev_cost;
                    $totals['curr_cost'] += $row->curr_cost;
                    $totals['to_date_cost'] += $row->to_date_cost;
                    $totals['remaining_cost'] += $row->remaining_cost;
                    $totals['at_completion_cost'] += $row->at_completion_cost;
                    $totals['at_completion_var'] += $row->at_completion_var;
                    $totals['cost_var'] += $row->cost_var;
                    $totals['to_date_allowable'] += $row->to_date_allowable;
                    $totals['to_date_cost_var'] = $totals['to_date_allowable'] - $totals['to_date_cost'];
                    return $totals;
                }, $totals);
            }, $totals);
        }, ['budget_cost' => 0, 'prev_cost' => 0, 'curr_cost' => 0, 'to_date_cost' => 0, 'remaining_cost' => 0, 'at_completion_var' => 0, 'at_completion_cost' => 0, 'cost_var' => 0, 'to_date_allowable' => 0, 'to_date_cost_var']);

        $sheet->fromArray([
            $name,
            '', '',
            $totals['budget_cost'] ?: 0,
            '', '',
            $totals['prev_cost'] ?: 0,
            '', '',
            $totals['curr_cost'] ?: 0,
            '', '', '', '',
            $totals['to_date_cost'] ?: 0,
            $totals['to_date_allowable'] ?: 0,
            $totals['to_date_cost_var'] ?: 0,
            '', '',
            $totals['remaining_cost'] ?: 0,
            '', '', '',
            $totals['at_completion_cost'] ?: 0,
            $totals['cost_var'] ?: 0,
            ''
        ], null, "A$counter", true);

        $sheet->getCell("A$counter")->getStyle()->applyFromArray($this->bold);
        $typeStyle = $sheet->getStyle("A$counter:Z$counter");
        $typeStyle->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_WHITE));
        $typeStyle->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('FF' . dechex(131) . dechex(163) . dechex(206)));


        foreach ($typeData as $discipline => $disciplineData) {
            ++$counter;
            $counter = $this->addExcelDiscipline($sheet, $discipline, $disciplineData, $counter);
        }

        return $counter;
    }

    private function addExcelDiscipline($sheet, $discipline, $disciplineData, $counter)
    {
        $totals = $disciplineData->reduce(function ($totals, $topMaterial) {
            $totals = $topMaterial->reduce(function ($totals, $row) {
                $totals['budget_cost'] += $row->budget_cost;
                $totals['prev_cost'] += $row->prev_cost;
                $totals['curr_cost'] += $row->curr_cost;
                $totals['to_date_cost'] += $row->to_date_cost;
                $totals['remaining_cost'] += $row->remaining_cost;
                $totals['at_completion_cost'] += $row->at_completion_cost;
                $totals['at_completion_var'] += $row->at_completion_var;
                $totals['cost_var'] += $row->cost_var;
                $totals['to_date_allowable'] += $row->to_date_allowable;
                $totals['to_date_cost_var'] = $totals['to_date_allowable'] - $totals['to_date_cost'];
                return $totals;
            }, $totals);

            return $totals;
        }, ['budget_cost' => 0, 'prev_cost' => 0, 'curr_cost' => 0, 'to_date_cost' => 0, 'remaining_cost' => 0, 'at_completion_var' => 0, 'at_completion_cost' => 0, 'cost_var' => 0, 'to_date_allowable' => 0, 'to_date_cost_var']);

        $sheet->fromArray([
            $discipline ?: 'General',
            '', '',
            $totals['budget_cost'] ?: 0,
            '', '',
            $totals['prev_cost'] ?: 0,
            '', '',
            $totals['curr_cost'] ?: 0,
            '', '', '', '',
            $totals['to_date_cost'] ?: 0,
            $totals['to_date_allowable'] ?: 0,
            $totals['to_date_cost_var'] ?: 0,
            '', '',
            $totals['remaining_cost'] ?: 0,
            '', '', '',
            $totals['at_completion_cost'] ?: 0,
            $totals['cost_var'] ?: 0,
            ''
        ], null, "A$counter", true);

        $sheet->getCell("A$counter")->getStyle()->applyFromArray($this->bold);
        $typeStyle = $sheet->getStyle("A$counter:Z$counter");
        $typeStyle->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('FF' . dechex(212) . dechex(228) . dechex(238)));


        $sheet->getRowDimension($counter)->setOutlineLevel(1)->setVisible(false)->setCollapsed(true);

        foreach ($disciplineData as $topMaterial => $topMaterialData) {
            if ($topMaterial) {
                ++$counter;
                $counter = $this->addExcelTopResource($sheet, $topMaterial, $topMaterialData, $counter);
            }
        }

        $resources = $disciplineData->get('', []);
        foreach ($resources as $resource) {
            ++$counter;
            $counter = $this->addExcelResource($sheet, $resource, $counter);
        }
        $sheet->getStyle("A{$counter}")->getAlignment()->setIndent(4);
        return $counter;
    }

    private function addExcelResource($sheet, $resource, $counter, $outlineLevel = 2)
    {

        $sheet->fromArray([
            $resource->resource_name,
            $resource->budget_qty? $resource->budget_cost / $resource->budget_qty : 0,
            $resource->budget_qty ?: 0,
            $resource->budget_cost ?: 0,
            $resource->prev_qty? $resource->prev_cost / $resource->prev_qty : 0,
            $resource->prev_qty ?: 0,
            $resource->prev_cost ?: 0,
            $resource->curr_qty? $resource->curr_cost / $resource->curr_qty : 0,
            $resource->curr_qty ?: 0,
            $resource->curr_cost ?: 0,
            $resource->to_date_qty? $resource->to_date_cost / $resource->to_date_qty : 0,
            $resource->to_date_qty ?: 0,
            $resource->to_date_allowable_qty ?: 0,
            $resource->to_date_allowable_qty - $resource->to_date_qty,
            $resource->to_date_cost ?: 0,
            $resource->to_date_allowable ?: 0,
            $resource->to_date_allowable - $resource->to_date_cost,
            $resource->remaining_qty? $resource->remaining_cost / $resource->remaining_qty : 0,
            $resource->remaining_qty ?: 0,
            $resource->remaining_cost ?: 0,
            $resource->at_completion_qty? $resource->at_completion_cost / $resource->at_completion_qty : 0,
            $resource->at_completion_qty ?: 0,
            $resource->qty_var ?: 0,
            $resource->at_completion_cost ?: 0,
            $resource->cost_var ?: 0,
            $resource->pw_index * 100,
        ], null, "A$counter", true);

        $sheet->getRowDimension($counter)->setOutlineLevel($outlineLevel)->setVisible(false)->setCollapsed(true);
        $sheet->getStyle("A{$counter}")->getAlignment()->setIndent($outlineLevel * 4);

        return $counter;
    }

    private function addExcelTopResource($sheet, $topMaterial, $topMaterialData, $counter)
    {

        $totals = $topMaterialData->reduce(function ($totals, $row) {
            $totals['budget_cost'] += $row->budget_cost;
            $totals['prev_cost'] += $row->prev_cost;
            $totals['curr_cost'] += $row->curr_cost;
            $totals['to_date_cost'] += $row->to_date_cost;
            $totals['remaining_cost'] += $row->remaining_cost;
            $totals['at_completion_cost'] += $row->at_completion_cost;
            $totals['at_completion_var'] += $row->at_completion_var;
            $totals['cost_var'] += $row->cost_var;
            $totals['to_date_allowable'] += $row->to_date_allowable;
            $totals['to_date_cost_var'] = $totals['to_date_allowable'] - $totals['to_date_cost'];

            return $totals;
        }, ['budget_cost' => 0, 'prev_cost' => 0, 'curr_cost' => 0, 'to_date_cost' => 0, 'remaining_cost' => 0, 'at_completion_var' => 0, 'at_completion_cost' => 0, 'cost_var' => 0, 'to_date_allowable' => 0, 'to_date_cost_var']);

        $sheet->fromArray([
            $topMaterial,
            '', '',
            $totals['budget_cost'] ?: 0,
            '', '',
            $totals['prev_cost'] ?: 0,
            '', '',
            $totals['curr_cost'] ?: 0,
            '', '', '', '',
            $totals['to_date_cost'] ?: 0,
            $totals['to_date_allowable'] ?: 0,
            $totals['to_date_cost_var'] ?: 0,
            '', '',
            $totals['remaining_cost'] ?: 0,
            '', '', '',
            $totals['at_completion_cost'] ?: 0,
            $totals['cost_var'] ?: 0,
            ''
        ], null, "A$counter", true);

        $sheet->getCell("A$counter")->getStyle()->applyFromArray($this->bold);
        $sheet->getStyle("A{$counter}")->getAlignment()->setIndent(8);

        $sheet->getRowDimension($counter)->setOutlineLevel(2)->setVisible(false)->setCollapsed(true);

        $typeStyle = $sheet->getStyle("A$counter:Z$counter");
        $typeStyle->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->setStartColor(new PHPExcel_Style_Color('FF' . dechex(251).dechex(228).dechex(208)));


        foreach($topMaterialData as $resource) {
            ++$counter;
            $counter = $this->addExcelResource($sheet, $resource, $counter, 3);
        }

        return $counter;
    }
}