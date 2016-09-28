<?php

use Illuminate\Database\Seeder;

class WBSLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $tree = [
//            ['Site 1', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 1'],
//            ['Site 1', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 2'],
//            ['Site 1', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 3'],
//            ['Site 1', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 4'],
//            ['Site 1', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 5'],
//            ['Site 1', 'Part 1', 'Building 1 - 2', 'Floor 1 - 2 - 1'],
//            ['Site 1', 'Part 1', 'Building 1 - 2', 'Floor 1 - 2 - 2'],
//            ['Site 1', 'Part 1', 'Building 1 - 2', 'Floor 1 - 2 - 3'],
//            ['Site 1', 'Part 2', 'Building 2 - 1', 'Floor 2 - 1 - 1'],
//            ['Site 1', 'Part 2', 'Building 2 - 1', 'Floor 2 - 1 - 2'],
//            ['Site 1', 'Part 2', 'Building 2 - 1', 'Floor 2 - 1 - 3'],
//            ['Site 1', 'Part 2', 'Building 2 - 2', 'Floor 2 - 2 - 1'],
//            ['Site 1', 'Part 2', 'Building 2 - 2', 'Floor 2 - 2 - 2'],
//            ['Site 2', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 1'],
//            ['Site 2', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 2'],
//            ['Site 2', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 3'],
//            ['Site 2', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 4'],
//            ['Site 2', 'Part 1', 'Building 1 - 1', 'Floor 1 - 1 - 5'],
//            ['Site 2', 'Part 1', 'Building 1 - 2', 'Floor 1 - 2 - 1'],
//        ];
//
//        $values = collect();
//
//        foreach ($tree as $row) {
//            $parent_id = 0;
//            foreach ($row as $name) {
//                if ($values->has($name)) {
//                    $parent_id = $values->get($name, 0);
//                } else {
//                    $level = \App\WbsLevel::create([
//                        'name' => $name,
//                        'project_id' => 1,
//                        'parent_id' => $parent_id
//                    ]);
//
//                    $values->put($name, $parent_id = $level->id);
//                }
//            }
//        }
    }
}
