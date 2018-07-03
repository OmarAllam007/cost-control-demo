<?php

namespace App\Export;

use App\BreakDownResourceShadow;
use App\StoreResource;
use App\Support\ActivityLog;
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
    private $allowable_cost;
    private $status = '';
    /** @var Collection */
    private $resource_logs;

    public function __construct(WbsLevel $wbs, $code)
    {
        $this->wbs = $wbs;
        $this->code = $code;
    }

    function handle()
    {
        $this->activity_name = BreakDownResourceShadow::where('wbs_id', $this->wbs->id)->where('code', $this->code)->value('activity');
        $this->status = $this->getStatus();
        $activityLog = new ActivityLog($this->wbs, $this->code);
        $this->resource_logs = $activityLog->handle();
        $costResources = BreakDownResourceShadow::with('actual_resources')
            ->where('wbs_id', $this->wbs->id)->where('code', $this->code)
            ->where('show_in_cost', 1)->get();
        $this->store_resources = $this->resource_logs->pluck('store_resources')->flatten();
        $this->budget_resources = $this->resource_logs->pluck('budget_resources')->flatten();
        $this->budget_cost = $costResources->sum('budget_cost');
        $this->actual_cost = $costResources->sum('to_date_cost');
        $this->allowable_cost = $costResources->sum('allowable_ev_cost');
        $this->variance = $costResources->sum('allowable_var');
        $this->first_upload_date = $this->store_resources->min('created_at')->format('d M Y H:i');
        $this->last_upload_date = $this->store_resources->max('created_at')->format('d M Y H:i');

        /*$this->budget_resources = BreakDownResourceShadow::with('actual_resources')->where('wbs_id', $this->wbs->id)->where('code', $this->code)->get();
        $resource_ids = $this->budget_resources->pluck('resource_id', 'resource_id');

        $this->store_resources = StoreResource::where('budget_code', $this->code)
            ->whereIn('resource_id', $resource_ids)->whereNull('row_ids')
            ->get();


        */
    }

    function download()
    {
        $this->handle();

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

    /**
     * @param PHPExcel_Worksheet $sheet
     */
    private function drivingResources($sheet)
    {
        $sheet->setCellValue('C2', $this->activity_name);
        $sheet->setCellValue('C3', $this->code);
        $sheet->setCellValue('C4', $this->status);

        $sheet->setCellValue('H2', $this->budget_cost);
        $sheet->setCellValue('H3', $this->actual_cost);
        $sheet->setCellValue('H4', $this->variance);

        $sheet->setCellValue('P2', $this->first_upload_date);
        $sheet->setCellValue('P3', $this->last_upload_date);

        $driving_resources = $this->resource_logs->filter(function($resource) {
            return $resource['important'];
        });

        /*$budget_driving = $this->budget_resources->filter(function($resource) {
            return $resource->important;
        })->groupBy('breakdown_resource_id');

        $resource_ids = $budget_driving->keys();

        $store_driving = $this->store_resources->filter(function($resource) use ($resource_ids) {
            return $resource_ids->contains($resource->breakdown_resource_id);
        })->groupBy('breakdown_resource_id');*/

        $this->start = 8;
        $this->row = 8;
        foreach ($driving_resources as $resource_log) {
            $this->buildResource($sheet, $resource_log);
        }

        $sheet->setSelectedCell("B{$this->start}");
        $sheet->setShowSummaryBelow(false);
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @param $resource_log
     * @throws
     */
    private function buildResource($sheet, $resource_log)
    {

        $budget_items = $resource_log['budget_resources'];
        $store_items = $resource_log['store_resources'];

        $budget_row = $start = $this->row;
        $sheet->fromArray([
            $resource_log['code'], $resource_log['name'], $resource_log['measure_unit'],
            $resource_log['unit_price'], $resource_log['budget_qty'], $resource_log['budget_cost']
        ], null, "B{$budget_row}", true);

        $sheet->fromArray([
            $resource_log['actual_unit_price'], $resource_log['actual_qty'], $resource_log['cost'],
            $resource_log['qty_var'], $resource_log['cost_var']
        ], null, "L{$budget_row}", true);

        $sheet->getRowDimension($budget_row)->setOutlineLevel(0)->setCollapsed(false)->setVisible(true);

        $sheet->getStyle("B{$budget_row}:S{$budget_row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['type' => 'solid', 'startcolor' => ['rgb' => 'EFF8FF']]
        ]);

        foreach ($budget_items as $budget_item) {
            ++$budget_row;
            $sheet->fromArray([
                $budget_item->resource_code, $budget_item->resource_name, $budget_item->measure_unit,
                $budget_item->unit_price, $budget_item->budget_unit, $budget_item->budget_cost, $budget_item->cost_account,
            ], null, "B{$budget_row}", true);

            $sheet->getRowDimension($budget_row)->setOutlineLevel(1)->setCollapsed(true)->setVisible(false);
        }

        $store_row = $start;
        foreach ($store_items as $store_item) {
            ++$store_row;
            $sheet->fromArray([
                $store_item->item_code, $store_item->item_desc, $store_item->measure_unit,
                $store_item->unit_price, $store_item->qty, $store_item->cost, null, null,
                $store_item->store_date, $store_item->created_at, $store_item->doc_no
            ], null, "I{$store_row}", true);

            $sheet->getRowDimension($store_row)->setOutlineLevel(1)->setCollapsed(true)->setVisible(false);
        }

        $max_row = max($budget_row, $store_row);
        $sheet->getStyle("B{$start}:S{$max_row}")->getBorders()->getInside()->setBorderStyle('thin');
        $sheet->getStyle("B{$start}:H{$max_row}")->getBorders()->getOutline()->setBorderStyle('medium');
        $sheet->getStyle("I{$start}:S{$max_row}")->getBorders()->getOutline()->setBorderStyle('medium');

        $this->row = ++$max_row;
    }

    private function getStatus()
    {
        $average = BreakDownResourceShadow::where('wbs_id', $this->wbs->id)->where('code', $this->code)->avg('progress');

        if ($average > 0 && $average < 100) {
            return 'In Progress';
        } elseif ($average == 100) {
            return 'Closed';
        }

        return 'Not Started';
    }
}