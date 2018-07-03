<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 20/05/2018
 * Time: 12:09 PM
 */

namespace App\Import\QtySurvey;

use App\BreakDownResourceShadow;
use App\BreakdownVariable;
use App\Survey;
use App\Unit;
use Illuminate\Support\Collection;
use PHPExcel;
use PHPExcel_IOFactory;
use function storage_path;
use function uniqid;

class QtySurveyModify
{
    /** @var \App\Project */
    private $project;

    private $file;

    /** @var Collection */
    private $wbs_levels;

    /** @var Collection */
    private $qty_surveys;

    /** @var Collection */
    private $failed;

    /** @var integer */
    private $success = 0;

    public function __construct($project, $file)
    {
        $this->project = $project;
        $this->file = $file;
        $this->failed = collect();

        Survey::flushEventListeners();

        $this->loadWbs();
        $this->loadQs();
        $this->loadUnits();
    }

    function import()
    {
        $sheet = PHPExcel_IOFactory::load($this->file)->getSheet(0);

        $rows = $sheet->getRowIterator(2);
        foreach ($rows as $row) {
            $data = $this->getDataFromCells($row);

            $this->handleRow($data);
        }

        $result = ['success' => $this->success];
        if ($this->failed->count()) {
            $result['failed'] = $this->createFailedExcel();
        }

        return $result;
    }

    function loadWbs()
    {
        $this->wbs_levels = $this->project->wbs_levels->keyBy(function ($level) {
            return strtolower($level->code);
        })->map(function ($level) {
            return $level->id;
        });
    }

    function loadQs()
    {
        $this->qty_surveys = $this->project->quantities
            ->groupBy('wbs_level_id')->map(function ($group) {
                return $group->keyBy(function ($qs) {
                    return strtolower($qs->cost_account);
                });
            });
    }

    private function getDataFromCells($row)
    {
        $cells = $row->getCellIterator();
        $data = [];
        foreach ($cells as $col => $cell) {
            $data[$col] = $cell->getValue();
        }
        return $data;
    }

    private function handleRow($data)
    {
        $wbs_code = strtolower($data['B']);
        $wbs_id = $this->wbs_levels->get($wbs_code);
        if (!$wbs_id) {
            $data['T'] = 'WBS not found';
            $this->failed->push($data);
            return false;
        }

        $cost_account = strtolower($data['E']);
        $qty_survey = $this->qty_surveys->get($wbs_id, collect())->get($cost_account);
        if (!$qty_survey) {
            $data['T'] = 'Cost account not found';
            $this->failed->push($data);
            return false;
        }

        $unit = strtolower($data['I']);
        $unit_id = $this->units->get($unit);
        if (!$unit_id) {
            $data['T'] = 'Invalid unit of measure';
            $this->failed->push($data);
            return false;
        }

        $qty_survey->description = $data['F'];
        $qty_survey->budget_qty = $data['G'];
        $qty_survey->eng_qty = $data['H'];

        $qty_survey->save();

        $this->handleVariables($data, $qty_survey);

        BreakDownResourceShadow::with('breakdown_resource')
            ->whereIn('wbs_id', $qty_survey->wbsLevel->getChildrenIds())
            ->where('cost_account', $qty_survey->cost_account)
            ->get()->each(function ($resource) use ($qty_survey) {
                $resource->breakdown_resource->budget_qty = $qty_survey->budget_qty;
                $resource->breakdown_resource->eng_qty = $qty_survey->eng_qty;
                $resource->breakdown_resource->save();
            });

        ++$this->success;
        return true;
    }

    private function loadUnits()
    {
        $this->units = Unit::select(['id', 'type'])->get()->keyBy(function ($unit) {
            return strtolower($unit->type);
        })->map(function ($unit) {
            return $unit->id;
        });
    }

    private function handleVariables($data, $qty_survey)
    {
        $start = ord('J');
        $end = ord('S');

        $order = 0;
        for ($c = $start; $c <= $end; ++$c) {
            ++$order;
            $cell = chr($c);
            if (!isset($data[$cell]) || $data[$cell] === '') {
                continue;
            }

            $variable = BreakdownVariable::firstOrNew(['qty_survey_id' => $qty_survey->id, 'display_order' => $order]);
            $variable->value = $data[$cell];
            $variable->save();
        }
    }

    private function createFailedExcel()
    {
        $headers = [
            'WPS Path', 'WBS Code', 'BOQ Item Code', 'QS Item Code', 'Cost Account',
            'Description', 'Budget Quantity', 'Engineer Quantity', 'Unit',
            'v1', 'v2', 'v3', 'v4', 'v5', 'v6', 'v7', 'v8', 'v9', 'v10', 'Error'
        ];

        $excel = new PHPExcel();
        $sheet = $excel->getSheet(0);

        $sheet->fromArray([
            'WPS Path', 'WBS Code', 'BOQ Item Code', 'QS Item Code', 'Cost Account', 'Description', 'Budget Quantity', 'Engineer Quantity', 'Unit',
            'v1', 'v2', 'v3', 'v4', 'v5', 'v6', 'v7', 'v8', 'v9', 'v10'
        ], null, "A1", true);

        $counter = 2;
        $varFields = range('J', 'S');
        foreach ($this->failed as $row) {
            foreach ($varFields as $c) {
                if (!isset($row[$c])) {
                    $row[$c] = '';
                }
            }

            $sheet->fromArray($row, null, "A{$counter}", true);
            ++$counter;
        }

        $autoColumns = ['B', 'C', 'D', 'E', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
        foreach ($autoColumns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(50);
        $sheet->getColumnDimension('F')->setAutoSize(false)->setWidth(80);

        $sheet->getStyle("A2:F{$counter}")->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle("G2:H{$counter}")->getNumberFormat()->setBuiltInFormatCode(40);
        $sheet->getStyle("J2:S{$counter}")->getNumberFormat()->setBuiltInFormatCode(40);
        $sheet->getStyle("A1:S1")->applyFromArray([
            'font' => ['bold' => true], 'fill' => [
                'type' => 'solid', 'startcolor' => ['rgb' => 'BCDEFA']
            ]
        ]);

        $filename = 'qs-failed-' . uniqid() . '.xlsx';
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save(storage_path('app/public/' . $filename));
        return '/storage/' . $filename;
    }
}