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
        \App\CSI_category::truncate();

        $seeds = [
            ['name' => 'SCAFFOLDING'],
            ['name' => 'SITE WORK'],
            ['name' => 'CONCRETE'],
            ['name' => 'MASONRY'],
            ['name' => 'INSULATION'],

        ];

        foreach ($seeds as $seed) {
            \App\CSI_category::create($seed);
        }


    }
}
