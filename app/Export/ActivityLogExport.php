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
use Illuminate\Support\Collection;
use PHPExcel_IOFactory;
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
        $sheet = $excel->getSheetByName('All Resources');

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


        $filename = storage_path('app/' . str_random() . '.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);

        return response()->download($filename, 'activity-log-' . $this->code . '.xlsx')
            ->deleteFileAfterSend(true);
    }
}