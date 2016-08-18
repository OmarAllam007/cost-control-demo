<?php

use Illuminate\Database\Seeder;

class ResourceSeed extends Seeder
{

    public function run()
    {
        \App\Resources::truncate();

        $seeds = [
            ['resource_type_id' => 1, 'resource_code' => 'G.01' , 'name'=>'Salaries','rate'=>2.33,'unit'=>'meter','waste'=>.02,'business_partner_id'=>1],
            ['resource_type_id' => 2, 'resource_code' => 'G.02' , 'name'=>'Site overHead','rate'=>2.33,'unit'=>'meter','waste'=>.02,'business_partner_id'=>1],

        ];

        foreach ($seeds as $seed) {
            \App\Resources::create($seed);
        }
    }
}
