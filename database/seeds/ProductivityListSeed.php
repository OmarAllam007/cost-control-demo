<?php

use Illuminate\Database\Seeder;

class ProductivityListSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        set_time_limit(60);
//
//        $path = storage_path('productivity_List.csv');
//        $handle = fopen($path, "r");
//
//        if ($handle !== FALSE) {
//            fgetcsv($handle);
//            $productivity_list = \App\ProductivityList::query()->pluck('type', 'id')->toArray();
//            while (($row = fgetcsv($handle)) !== FALSE) {
//                if (in_array($row[1], $productivity_list)) {
//                    continue;
//                } else {
//                    \App\ProductivityList::create([
//                        'name' => $row[0],
//                        'type' => $row[1],
//                        'discipline' => $row[2],
//                    ]);
//                }
//            }
//        }
//
//        fclose($handle);
    }
}
