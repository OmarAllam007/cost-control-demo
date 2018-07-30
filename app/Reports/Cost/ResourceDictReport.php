<?php

namespace App\Reports\Cost;

use App\BreakDownResourceShadow;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Support\ResourceTypesTree;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet;

class ResourceDictReport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    /** @var Collection */
    private $resourceData;

    /** @var Collection */
    private $types;

    /** @var integer */
    private $row;

    function __construct($period)
    {
        $this->project = $period->project;
        $this->period = $period;
    }

    public function run()
    {
//        $tree = $this->buildTree();
        $project = $this->project;
        $period = $this->period;

        $periods = $project->periods()->readyForReporting()->pluck('name', 'id');

        $typesTree = new ResourceTypesTree();
        $this->types = $typesTree->setIncludeResources(false)->get();

        $types = BreakDownResourceShadow::whereProjectId($this->project->id)
            ->selectRaw('resource_type_id, resource_type')->orderBy('resource_type')->pluck('resource_type', 'resource_type_id');

        $disciplines = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT boq_discipline')->orderBy('boq_discipline')->pluck('boq_discipline');

        $topMaterials = MasterShadow::wherePeriodId($this->period->id)
            ->selectRaw('DISTINCT top_material')->orderBy('top_material')->pluck('top_material')->filter();

        $query = MasterShadow::forPeriod($this->period)->resourceDictReport();

        $this->resourceData = $this->applyFilters($query)->get()->groupBy('resource_type_id');

        $tree = $this->buildTree();

        if ($tree->has(8)) {
            $tree->get(8, collect())->first()->db_resources->each(function ($resource) {
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
        }

        return compact('project', 'tree', 'periods', 'types', 'disciplines', 'topMaterials', 'period');
    }

    private function buildTree($parent = null)
    {
        if (!$parent) {
            $types = $this->types;
        } else {
            $types = $parent->subtree;

        }

        return $types->map(function($type) {
            $type->subtree = $this->buildTree($type);
            $type->db_resources = $this->resourceData->get($type->id, collect());

            $type->budget_cost = $type->subtree->sum('budget_cost') + $type->db_resources->sum('budget_cost');
            $type->to_date_cost = $type->subtree->sum('to_date_cost') + $type->db_resources->sum('to_date_cost');
            $type->to_date_allowable = $type->subtree->sum('to_date_allowable') + $type->db_resources->sum('to_date_allowable');
            $type->to_date_var = $type->subtree->sum('to_date_var') + $type->db_resources->sum('to_date_var');
            $type->prev_cost = $type->subtree->sum('prev_cost') + $type->db_resources->sum('prev_cost');
            $type->curr_cost = $type->subtree->sum('curr_cost') + $type->db_resources->sum('curr_cost');
            $type->prev_allowable = $type->subtree->sum('prev_allowable') + $type->db_resources->sum('prev_allowable');
            $type->prev_cost_var = $type->subtree->sum('prev_cost_var') + $type->db_resources->sum('prev_cost_var');
            $type->remaining_cost = $type->subtree->sum('remaining_cost') + $type->db_resources->sum('remaining_cost');
            $type->at_completion_cost = $type->subtree->sum('at_completion_cost') + $type->db_resources->sum('at_completion_cost');
            $type->cost_var = $type->subtree->sum('cost_var') + $type->db_resources->sum('cost_var');

            return $type;
        })->filter(function($type) {
            return $type->subtree->count() || $type->db_resources->count();
        });
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
            $query->where('master_shadows.resource_type_id', $type);
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

        $start = 11;
        $this->row = $start;

        $this->bold = ['font' => ['bold' => true]];

        $accent1 = [
            'fill' => [
                'type' => 'solid', 'startColor' => dechex(131) . dechex(163) . dechex(206)
            ],
            'font' => ['color' => 'ffffff']
        ];

        foreach ($tree as $type) {
            $counter = $this->addExcelType($sheet, $type);
        }

        $sheet->getStyle("B{$start}:Z{$this->row}")->getNumberFormat()->setBuiltInFormatCode(40);

        $totalsStyles = $sheet->getStyle("A{$this->row}:Z{$this->row}");
        $totalsStyles->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new PHPExcel_Style_Color('DAEEF3'));
        $totalsStyles->getFont()->setBold(true);

//        $sheet->getStyle("N{$row}:N{$this->row}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("Q{$row}:Q{$this->row}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("W{$row}:W{$this->row}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("Y{$row}:Y{$this->row}")->setConditionalStyles([$varCondition]);
//        $sheet->getStyle("Z{$row}:Z{$this->start}")->setConditionalStyles([$varCondition]);

        $sheet->setShowGridlines(false);

        $sheet->setShowSummaryBelow(false);

        $sheet->setTitle('Resource Dict (Cost)');

        return $sheet;
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @param $type
     * @return mixed
     */
    private function addExcelType($sheet, $type, $depth = 0)
    {
        ++$this->row;
        $sheet->fromArray([
            $type->name, '', '',
            $type->budget_cost ?: 0, '', '',
            $type->prev_cost ?: 0, '', '',
            $type->curr_cost ?: 0, '', '', '', '',
            $type->to_date_cost ?: 0,
            $type->to_date_allowable ?: 0,
            $type->to_date_var ?: 0, '', '',
            $type->remaining_cost ?: 0, '', '', '',
            $type->at_completion_cost ?: 0,
            $type->cost_var ?: 0,
        ], null, "A{$this->row}", true);

        $sheet->getStyle("A{$this->row}:Z{$this->row}")->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle("A{$this->row}")->applyFromArray(['alignment' => ['indent' => $depth * 4]]);
        $sheet->getRowDimension($this->row)->setOutlineLevel(min(8, $depth + 1))->setVisible($depth == 0)->setCollapsed($depth > 0);

        foreach ($type->subtree as $subtype) {
            $this->addExcelType($sheet, $subtype, $depth + 1);
        }

        foreach ($type->db_resources as $resource) {
            $this->addExcelResource($sheet, $resource, $depth + 1);
        }
    }

    private function addExcelResource($sheet, $resource, $depth)
    {
        ++$this->row;

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
            $resource->to_date_var,
            $resource->remaining_qty? $resource->remaining_cost / $resource->remaining_qty : 0,
            $resource->remaining_qty ?: 0,
            $resource->remaining_cost ?: 0,
            $resource->at_completion_qty? $resource->at_completion_cost / $resource->at_completion_qty : 0,
            $resource->at_completion_qty ?: 0,
            $resource->qty_var ?: 0,
            $resource->at_completion_cost ?: 0,
            $resource->cost_var ?: 0,
            $resource->pw_index * 100,
        ], null, "A$this->row", true);

        $sheet->getRowDimension($this->row)->setOutlineLevel(min(8, $depth + 1))->setVisible(false)->setCollapsed(true);
        $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent($depth * 4);

        if ($resource->top_material) {
            $sheet->getStyle("A{$this->row}:Z{$this->row}")
                ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->setStartColor(new PHPExcel_Style_Color('FF' . dechex(251).dechex(228).dechex(208)));
        }
    }
}