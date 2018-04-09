<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 05/04/2018
 * Time: 2:49 PM
 */

namespace App\Export;


use App\BreakDownResourceShadow;
use App\StoreResource;
use App\WbsLevel;
use function collect;
use Illuminate\Support\Collection;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use function response;
use function storage_path;
use function str_random;

class ActivityLogExport
{

    /** @var WbsLevel */
    private $wbs;

    private $code;

    /** @var Collection */
    private $budget_resources;

    /** @var Collection */
    private $store_resources;
    private $activity_name;
    private $actual_cost;
    private $budget_cost;
    private $variance;

    public function __construct(WbsLevel $wbs, $code)
    {
        $this->wbs = $wbs;
        $this->code = $code;
    }

    function run()
    {
        $this->budget_resources = BreakDownResourceShadow::with('actual_resources')->where('wbs_id', $this->wbs->id)->where('code', $this->code)->get();
        $resource_ids = $this->budget_resources->pluck('resource_id', 'resource_id');

        $this->store_resources = StoreResource::where('budget_code', $this->code)
            ->whereIn('resource_id', $resource_ids)
            ->get();

        $this->activity_name = $this->budget_resources->first()->activity;
        $this->budget_cost = $this->budget_resources->sum('budget_cost');
        $this->actual_cost = $this->budget_resources->sum('to_date_cost');
        $this->variance = $this->budget_cost - $this->actual_cost;
        $average = $this->budget_resources->avg('progress');
        if ($average > 0 && $average < 100) {
            $this->status = 'In Progress';
        } elseif ($average == 100) {
            $this->status = 'Closed';
        } else {
            $this->status = 'Not Started';
        }

        $this->first_upload_date = $this->store_resources->min('created_at');
        $this->last_upload_date = $this->store_resources->max('created_at');
    }

    function download()
    {
        $this->run();

        $excel = PHPExcel_IOFactory::load(storage_path('templates/activity-log.xlsx'));
        $sheet = $excel->getSheet(1);
        $this->allResources($sheet);

        $sheet = $excel->getSheet(0);
        $this->drivingResources($sheet);


        $filename = storage_path('app/' . str_random() . '.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);

        return response()->download($filename, 'activity-log-' . $this->code . '.xlsx')
            ->deleteFileAfterSend(true);
    }

    private function allResources($sheet)
    {
        $sheet->setCellValue('C2', $this->activity_name);
        $sheet->setCellValue('C3', $this->code);
        $sheet->setCellValue('C4', $this->status);

        $sheet->setCellValue('H2', $this->budget_cost);
        $sheet->setCellValue('H3', $this->actual_cost);
        $sheet->setCellValue('H4', $this->variance);

        $sheet->setCellValue('P2', $this->first_upload_date);
        $sheet->setCellValue('P3', $this->last_upload_date);

        $start = $budget_row = 8;
        foreach ($this->budget_resources as $resource) {
            $sheet->fromArray([
                $resource->resource_code, $resource->resource_name, $resource->measure_unit,
                $resource->unit_price, $resource->budget_unit, $resource->budget_cost, $resource->cost_account,
            ], null, "B{$budget_row}", true);
            ++$budget_row;
        }

        $store_row = $start;
        foreach ($this->store_resources as $resource) {
            $sheet->fromArray([
                $resource->item_code, $resource->item_desc, $resource->measure_unit,
                $resource->unit_price, $resource->qty, $resource->cost, $resource->store_date,
                $resource->created_at, $resource->doc_no,
            ], null, "I{$store_row}", true);

            ++$store_row;
        }

        $maxRow = max($budget_row, $store_row);
        $sheet->getStyle("B{$start}:Q{$maxRow}")->getBorders()->getAllBorders()->setBorderStyle('thin');
        $sheet->getStyle("B{$start}:Q{$maxRow}")->getBorders()->getOutline()->setBorderStyle('medium');
        $sheet->getStyle("H{$start}:H{$maxRow}")->getBorders()->getRight()->setBorderStyle('medium');
    }

    private function drivingResources($sheet)
    {
        $budget_driving = $this->budget_resources->filter(function($resource) {
            return $resource->important;
        })->groupBy('resource_id');

        $resource_ids = $budget_driving->keys();

        $store_driving = $this->store_resources->filter(function($resource) use ($resource_ids) {
            return $resource_ids->contains($resource->resource_id);
        })->groupBy('resource_id');

        $this->start = 8;
        $this->row = 8;
        foreach ($resource_ids as $id) {
            $budget_items = $budget_driving->get($id);
            $store_items = $store_driving->get($id, collect());

            $this->buildResource($sheet, $budget_items, $store_items);
        }

    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @param Collection         $budget_items
     * @param Collection         $store_items
     */
    private function buildResource($sheet, $budget_items, $store_items)
    {
        $budget_row = $start = $this->row;

        foreach ($budget_items as $budget_item) {
            $sheet->fromArray([
                $budget_item->resource_code, $budget_item->resource_name, $budget_item->measure_unit,
                $budget_item->unit_price, $budget_item->budget_unit, $budget_item->budget_cost, $budget_item->cost_account,
            ], null, "B{$budget_row}", true);
            ++$budget_row;
        }

        $store_row = $start;
        foreach ($store_items as $store_item) {
            $sheet->fromArray([
                $store_item->item_code, $store_item->item_desc, $store_item->measure_unit,
                $store_item->unit_price, $store_item->qty, $store_item->cost, null, null,
                $store_item->store_date, $store_item->created_at, $store_item->doc_no
            ], null, "I{$store_row}", true);
            ++$store_row;
        }

        $max_row = max($budget_row, $store_row) - 1;
        $sheet->getStyle("B{$start}:S{$max_row}")->getBorders()->getInside()->setBorderStyle('thin');
        $sheet->getStyle("B{$start}:H{$max_row}")->getBorders()->getOutline()->setBorderStyle('medium');
        $sheet->getStyle("I{$start}:S{$max_row}")->getBorders()->getOutline()->setBorderStyle('medium');

        $this->row = ++$max_row;
    }
}