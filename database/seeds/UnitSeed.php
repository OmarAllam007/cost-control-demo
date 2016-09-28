<?php

use Illuminate\Database\Seeder;

class UnitSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

//        set_time_limit(60);
//        \App\Productivity::truncate();
//        $path = storage_path('files\items.csv');
//        $handle = fopen($path, "r");
//
//        if ($handle !== FALSE) {
//            fgetcsv($handle);
//            $productivity_category = \App\CSI_category::query()->pluck('id', 'name')->toArray();
//            $unit = \App\Unit::query()->pluck('id', 'type')->toArray();
//
//
//            while (($row = fgetcsv($handle)) !== FALSE) {
//                $units = \App\Unit::where('type', $row[1])->first();
//                if (is_null($units)) {
//                    if ($row[1] == '' || $row[1] == ',,' || $row[1] == '"' || $row[1] == ' ') {
//
//                    } else {
//                        \App\Unit::create([
//                            'type' => $row[1],
//                        ]);
//                    }
//                }
//            }
//
//
//        }
    }
}
