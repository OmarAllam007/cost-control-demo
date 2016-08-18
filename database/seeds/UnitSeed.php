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
        \App\Unit::truncate();

        $seeds = [
            ['type' => 'م2'],
            ['type' => 'عدد'],
            ['type' => 'م ط'],
        ];

        foreach ($seeds as $seed) {
            \App\Unit::create($seed);
        }


    }
}
