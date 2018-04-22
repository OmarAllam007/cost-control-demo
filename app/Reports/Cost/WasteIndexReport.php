<?php

namespace App\Reports\Cost;

use App\MasterShadow;
use App\Period;
use App\ResourceType;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Writers\CellWriter;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;

class WasteIndexReport
{
    protected $total_varaince;
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    /** @var Collection */
    private $types;

    /** @var Collection */
    private $resources;

    /** @var Collection */
    private $tree;

    /** @var float */
    private $total_pw_index = 0;

    private $start = 11;
    private $row = 11;


    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $this->types = ResourceType::all()->groupBy('parent_id');

        $query = MasterShadow::from('master_shadows as sh')
            ->where('sh.period_id', $this->period->id)
            ->where('sh.resource_type_id', 3)
            ->where('to_date_qty', '>', 0)
            ->join('resources as r', 'sh.resource_id', '=', 'r.id')
            ->selectRaw('sh.resource_name, r.resource_type_id, sum(sh.to_date_qty) as to_date_qty')
            ->selectRaw('sum(sh.allowable_qty) as allowable_qty, sum(sh.to_date_cost) / sum(sh.to_date_qty) as to_date_unit_price')
            ->selectRaw('sum(to_date_cost) as to_date_cost')
            ->selectRaw('sum(sh.allowable_ev_cost - to_date_cost) as to_date_cost_var')
            ->selectRaw('sum(to_date_qty_var) as qty_var, sum(pw_index) as pw_index')
            ->groupBy(['sh.resource_name', 'r.resource_type_id']);

        $this->applyFilters($query);

        $this->resources = $query->get()->groupBy('resource_type_id');

        $this->tree = $this->buildTree();

        $allowable_cost = $this->tree->sum('allowable_cost');
        $this->total_varaince = $this->tree->sum('variance');

        if ($allowable_cost) {
            $this->total_pw_index = $this->total_varaince * 100 / $allowable_cost;
        }

        return ['project' => $this->project, 'period' => $this->period, 'tree' => $this->tree, 'total_pw_index' => $this->total_pw_index, 'total_variance' => $this->total_varaince];
    }

    private function buildTree($parent = 3)
    {
        return $this->types->get($parent, collect())->map(function($type) {
            $type->subtree = $this->buildTree($type->id);

            $type->resources_list = $this->resources->get($type->id, collect())->map(function($resource) {
                $resource->pw_index = 0;
                $resource->variance = ($resource->allowable_qty - $resource->to_date_qty) * $resource->to_date_unit_price;
                $resource->allowable_cost = $resource->allowable_qty * $resource->to_date_unit_price;

                if ($resource->allowable_cost) {
                    $resource->pw_index = $resource->variance * 100 / $resource->allowable_cost;
                }

                return $resource;
            });

            $type->allowable_cost = $type->resources_list->sum('allowable_cost') + $type->subtree->sum('allowable_cost');
            $type->to_date_cost = $type->resources_list->sum('to_date_cost') + $type->subtree->sum('to_date_cost');
            $type->variance = $type->resources_list->sum('variance') + $type->subtree->sum('variance');
            $type->pw_index = 0;
            if ($type->allowable_cost) {
                $type->pw_index = $type->variance * 100 / $type->allowable_cost;
            }

            return $type;
        })->reject(function ($type) {
            return $type->subtree->isEmpty() && $type->resources_list->isEmpty();
        });
    }

    private function applyFilters($query)
    {
        if ($types = request('type')) {
            $query->whereIn('r.resource_type_id', $types);
        }

        if ($resource = request('resource')) {
            $term = "%$resource%";
            $query->where(function ($q) use ($term) {
                $q->where('sh.resource_code', 'like', $term)
                    ->orWhere('resource_name', 'like', $term);
            });
        }

        if (request('negative')) {
            $query->where('qty_var', '<', 0);
        }
    }

    function excel()
    {
        $excel = new \PHPExcel();

        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet());
        $filename = storage_path('app/waste-index-' . uniqid() . '.xlsx');
        $writer = new \PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save($filename);

        $name = slug($this->project->name) . '_' . slug($this->period->name) . '_waste-index.xlsx';
        return \Response::download($filename, $name)->deleteFileAfterSend(true);

//        \Excel::create(slug($this->project->name) . '-waste_index', function(LaravelExcelWriter $excel) {
//
//            $excel->sheet(0, function($sheet) {
//                $this->sheet($sheet);
//            });
//
//            $excel->export('xlsx');
//        });
    }

    function sheet()
    {
        $this->run();

        $sheet = PHPExcel_IOFactory::load(storage_path('templates/waste-index.xlsx'))->getSheet(0);

        $sheet->setCellValue('A4', "Project: {$this->project->name}");
        $sheet->setCellValue('A5', "Issue Date: " . date('d M Y'));
        $sheet->setCellValue('A6', "Period: {$this->period->name}");


        $sheet->setCellValue("F{$this->row}", $this->tree->sum('allowable_cost'));
        $sheet->setCellValue("G{$this->row}", $this->tree->sum('to_date_cost'));
        $sheet->setCellValue("H{$this->row}", $this->tree->sum('to_date_cost_var'));
        $sheet->setCellValue("I{$this->row}", $this->total_pw_index / 100);

        $this->tree->each(function($type) use ($sheet) {
            $this->buildExcelTypes($sheet, $type);
        });

        $sheet->getStyle("B{$this->start}:H{$this->row}")->getNumberFormat()->setBuiltInFormatCode(40);
        $sheet->getStyle("I{$this->start}:I{$this->row}")->getNumberFormat()->setBuiltInFormatCode(10);
        $sheet->setShowSummaryBelow(false);
        return $sheet;
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @param $type
     * @param int $depth
     */
    function buildExcelTypes($sheet, $type, $depth = 0)
    {
        ++$this->row;

        $sheet->fromArray([
            $type->name, '', '', '', '',
            $type->allowable_cost, $type->to_date_cost, $type->variance, $type->pw_index / 100
        ], null, "A{$this->row}", true);

        $sheet->getStyle("A{$this->row}:I{$this->row}")->getFont()->setBold(true);

        if ($depth) {
            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setVisible(false)->setCollapsed(true);

            $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent($depth * 6);
        }

        ++$depth;
        $type->subtree->each(function($subtype) use ($sheet, $depth) {
            $this->buildExcelTypes($sheet, $subtype, $depth);
        });

        $type->resources_list->each(function($resource) use ($sheet, $depth) {
            ++$this->row;
            $sheet->fromArray([
                $resource->resource_name, $resource->to_date_unit_price ?: 0, $resource->to_date_qty,
                $resource->allowable_qty ?: 0, $resource->qty_var ?: 0,
                $resource->allowable_cost ?: 0, $resource->to_date_cost ?: 0, $resource->variance ?: 0, $resource->pw_index / 100
            ], null, "A{$this->row}", true);

            $sheet->getRowDimension($this->row)
                ->setOutlineLevel(min($depth, 7))
                ->setVisible(false)->setCollapsed(true);

            $sheet->getStyle("A{$this->row}")->getAlignment()->setIndent($depth * 6);
        });
    }

}