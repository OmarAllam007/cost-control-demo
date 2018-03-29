<?php
namespace App\Http\Controllers\Reports\CostReports;

use App\BreakDownResourceShadow;
use App\MasterShadow;
use App\Period;
use App\Project;
use Illuminate\Database\Eloquent\Builder;
use PHPExcel_IOFactory;
use PHPExcel_Style_Color;
use PHPExcel_Style_Conditional;
use PHPExcel_Worksheet_MemoryDrawing;

class VarianceAnalysisReport
{

    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $project = $this->project;
        $period = $this->period;
        $tree = $this->buildTree();

        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        $types = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('DISTINCT resource_type')->orderBy('resource_type')->pluck('resource_type');

        $disciplines = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT boq_discipline')->orderBy('boq_discipline')->pluck('boq_discipline');

        $topMaterials = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT top_material')->orderBy('top_material')->pluck('top_material')->filter();

        return compact('tree', 'project', 'periods', 'types', 'disciplines', 'period', 'topMaterials');
    }

    function buildTree()
    {
        $query = MasterShadow::forPeriod($this->period)->varAnalysisReport();

        $resourceData = $this->applyFilters($query)->get();

        $tree = $resourceData->groupBy('resource_type')->map(function($typeGroup) {
            $disciplines = $typeGroup->groupBy('boq_discipline')->map(function($group) {
                $resources = $group->map(function($resource) {
                    $resource->price_var = $resource->budget_unit_price - $resource->to_date_unit_price;
                    $resource->price_cost_var = $resource->price_var * $resource->to_date_qty;

                    $resource->qty_var = $resource->to_date_allowable_qty - $resource->to_date_qty;
                    $resource->qty_cost_var = $resource->qty_var * $resource->budget_unit_price;

                    return $resource;
                });

                return [
                    'resources' => $resources, 'price_cost_var' => $resources->sum('price_cost_var'),
                    'qty_cost_var' => $resources->sum('qty_cost_var'),
                    'cost_unit_price_var' => $resources->sum('cost_unit_price_var'),
                    'cost_qty_var' => $resources->sum('cost_qty_var')
                ];
                //, 'to_date_variance' => $group->sum()];
            });

            return [
                'disciplines' => $disciplines, 'price_cost_var' => $disciplines->sum('price_cost_var'),
                'qty_cost_var' => $disciplines->sum('qty_cost_var'),
                'cost_unit_price_var' => $disciplines->sum('cost_unit_price_var'),
                'cost_qty_var' => $disciplines->sum('cost_qty_var')
            ];
        });

        return $tree;
    }

    function excel()
    {
        $excel = new \PHPExcel();
        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet(), 0);

        $filename = storage_path('app/' . uniqid('varanalysis_') . '.xlsx');
        \PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);

        return \Response::download($filename,
            slug($this->project->name) . '_' . slug($this->period->name) . '_var_analysis.xlsx'
        )->deleteFileAfterSend(true);
    }

    function sheet()
    {
        $data = $this->run();
        $tree = $data['tree'];
        $project = $data['project'];
        $period = $data['period'];

        $excel = \PHPExcel_IOFactory::load(storage_path('templates/cost-var-analysis.xlsx'));
        $sheet = $excel->getSheet(0);

        $varCondition = new PHPExcel_Style_Conditional();
        $varCondition->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS);
        $varCondition->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_LESSTHAN);
        $varCondition->addCondition(0);
        $varCondition->getStyle()->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

        $projectCell = $sheet->getCell('A4');
        $issueDateCell = $sheet->getCell('A5');
        $periodCell = $sheet->getCell('A6');

        $projectCell->setValue($projectCell->getValue() . ' ' . $project->name);
        $issueDateCell->setValue($issueDateCell->getValue() . ' ' . date('d M Y'));
        $periodCell->setValue($periodCell->getValue() . ' ' . $period->name);

        $logo = imagecreatefrompng(public_path('images/kcc.png'));
        $drawing = new PHPExcel_Worksheet_MemoryDrawing();
        $drawing->setName('Logo')->setImageResource($logo)
            ->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
            ->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
            ->setCoordinates('L2')->setWorksheet($sheet);

        $start = 11;
        $counter = $start;

        $bold = ['font' => ['bold' => true]];

        $accent1 = [
            'fill' => [
                'type' => 'solid', 'startColor' => dechex(131).dechex(163).dechex(206)
            ],
            'font' => ['color' => 'ffffff']
        ];

        foreach($tree as $type => $typeData){

            ++$counter;

            $sheet->fromArray([
                $type, '', '', '', '', '',
                $typeData['price_cost_var'], '', '', '',
                $typeData['qty_cost_var']?: '0.00', //K
                $typeData['cost_unit_price_var']?: '0.00', //L
                $typeData['cost_qty_var']?: '0.00'
            ], '', "A{$counter}");
            $sheet->getCell("A{$counter}")->getStyle()->applyFromArray($bold);

            foreach ($typeData['disciplines'] as $discipline => $disciplineData) {
                ++$counter;
                $sheet->fromArray([
                    '    ' . ($discipline?: 'General'), '', '', '', '', '',
                    $disciplineData['price_cost_var'], '', '', '',
                    $disciplineData['qty_cost_var']?: '0.00', //K
                    $disciplineData['cost_unit_price_var']?: '0.00', //L
                    $disciplineData['cost_qty_var']?: '0.00'
                ], '', "A{$counter}");
                $sheet->getRowDimension($counter)->setOutlineLevel(1)->setCollapsed(true)->setVisible(false);
                $sheet->getCell("A{$counter}")->getStyle()->applyFromArray($bold);

                foreach ($disciplineData['resources'] as $resource) {
                    ++$counter;

                    $sheet->fromArray([
                        '        ' . $resource->resource_name, //A
                        $resource->budget_unit_price?: '0.00', //B
                        $resource->prev_unit_price?: '0.00', //C
                        $resource->curr_unit_price?: '0.00', //D
                        $resource->to_date_unit_price?: '0.00', //E
                        $resource->price_var ?: '0.00', //F
                        $resource->price_cost_var ?: '0.00', //G
                        $resource->to_date_qty?: '0.00', //H
                        $resource->to_date_allowable_qty?: '0.00', //I
                        $resource->qty_var?: '0.00', //J
                        $resource->qty_cost_var ?: '0.00', //K
                        $resource->cost_unit_price_var?: '0.00', //L
                        $resource->cost_qty_var?: '0.00', //M
                    ], null, "A{$counter}", true);
                    $sheet->getRowDimension($counter)->setOutlineLevel(2)->setCollapsed(true)->setVisible(false);
                }
            }

        }

        $sheet->getStyle("B{$start}:M{$counter}")->getNumberFormat()->setBuiltInFormatCode(40);

        /*$totalsStyles = $sheet->getStyle("A{$counter}:Z{$counter}");
        $totalsStyles->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('DAEEF3'));
        $totalsStyles->getFont()->setBold(true);*/

        $sheet->getStyle("F{$start}:F{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("G{$start}:G{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("J{$start}:J{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("L{$start}:L{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("M{$start}:M{$counter}")->setConditionalStyles([$varCondition]);

        $sheet->setShowSummaryBelow(false);
        $sheet->setSelectedCell("A{$start}");

        $sheet->setTitle('Variance Analysis');

        return $sheet;
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

        if ($discipline = $request->get('discipline')) {
            // We have to consider that resources without discipline are mapped to general also
            if (strtolower($discipline) == 'general') {
                $query->where(function($q) {
                    $q->where('boq_discipline', 'general')->orWhere('boq_discipline', '')->orWhereNull('boq_discipline');
                });
            } else {
                $query->where('boq_discipline', $discipline);
            }
        }

        if ($top = $request->get('top')) {
            // We have to consider that resources without discipline are mapped to general also
            if (strtolower($top) == 'all') {
                $query->whereNotNull('top_material')->where('top_material', '!=', '');
            } elseif (strtolower($top) == 'other') {
                $query->where(function($q) {
                    $q->whereNull('top_material')->orWhere('top_material', '');
                });
            } else {
                $query->where('top_material', $top);
            }
        }

        if ($resource = $request->get('resource')) {
            $query->where(function($q) use ($resource) {
                $term = "%$resource%";
                $q->where('resource_code', 'like', $term)->orWhere('resource_name', 'like', $term);
            });
        }

        return $query;
    }
}