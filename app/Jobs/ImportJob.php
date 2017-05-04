<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 9/4/16
 * Time: 3:29 PM
 */

namespace App\Jobs;


use App\Unit;
use App\UnitAlias;
use Illuminate\Support\Collection;

class ImportJob extends Job
{

    /**
     * @var Collection
     */
    protected $units;

    protected function getDataFromCells($cells)
    {
        $data = [];
        /** @var \PHPExcel_Cell $cell */
        /** @var \PHPExcel_Worksheet_CellIterator $cells */
        foreach ($cells as $cell) {
            $data[] = $cell->getFormattedValue()?: $cell->getValue()?: '';
        }
        return array_map('trim', $data);
    }

    protected function getUnit($unit)
    {
        if (!$this->units) {
            $this->units = collect();
            Unit::all()->each(function ($unit) {
                $this->units->put(mb_strtolower($unit->type), $unit->id);
            });

            UnitAlias::all()->each(function($unit) {
                $this->units->put(mb_strtolower($unit->name), $unit->unit_id);
            });
        }

        $unit = trim($unit);
        $key = mb_strtolower($unit);
        return $this->units->get($key, 0);
    }
}