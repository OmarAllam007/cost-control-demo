<?php

use Illuminate\Database\Seeder;

class CSICategorySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        set_time_limit(60);
//        $start = microtime(true);
//        $path = storage_path('files\category.csv');
//        $handle = fopen($path, "r");
//
//        if ($handle !== FALSE) {
//            fgetcsv($handle);
//            $productivity_category = \App\CsiCategory::query()->pluck('name', 'id')->toArray();
//
//            while (($row = fgetcsv($handle)) !== FALSE) {
//                $levels = array_filter($row);
//                $parent_id = 0;
//                foreach ($levels as $level) { //fill categories
//                    if (!isset($productivity_category[$level])) {
//                        $category = \App\CsiCategory::create([
//                            'name' => $level,
//                            'parent_id' => $parent_id,
//                        ]);
//
//                        $productivity_category[$level] = $parent_id = $category->id;
//
//                    } else {
//                        $parent_id = $productivity_category[$level];
//                    }
//                }
//                //fill productivies
//
//            }
//
//
//        }
//
//        fclose($handle);

    }
}
