<?php
/**
 * Created by PhpStorm.
 * User: hazem
 * Date: 9/4/16
 * Time: 3:29 PM
 */

namespace App\Jobs;


use App\Unit;
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
            $data[] = $cell->getValue() ?: '';
        }
        return $data;
    }

    protected function getUnit($unit)
    {
        if (!$this->units) {
            $this->units = collect();
            Unit::all()->each(function ($unit) {
                $this->units->put(mb_strtolower($unit->type), $unit->id);
            });
        }
        $unit = trim($unit);

        if (!$unit) {
            return 0;
        }

        $key = mb_strtolower($unit);
        if ($this->units->has($key)) {
            return $this->units->get($key);
        }

        $unitObject = Unit::create(['type' => $unit]);
        $this->units->put(mb_strtolower($unit), $unitObject->id);
        return $unitObject->id;
    }
}