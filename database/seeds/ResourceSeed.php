<?php

use Illuminate\Database\Seeder;

class ResourceSeed extends Seeder
{

    public function run()
    {

        $resources = \App\Resources::all();
        $codes = [];
        foreach ($resources as $resource){
            if(in_array($resource->resource_code,$codes)){
                $resource->delete();
            }
            else{
                $codes[] = $resource->resource_code;
            }

        }

//        $seeds = [
//            ['resource_type_id' => 1, 'resource_code' => 'G.01' , 'name'=>'Salaries','rate'=>2.33,'unit'=>1,'waste'=>.02,'business_partner_id'=>1],
//            ['resource_type_id' => 2, 'resource_code' => 'G.02' , 'name'=>'Site overHead','rate'=>2.33,'unit'=>2,'waste'=>.02,'business_partner_id'=>1],
//
//        ];
//
//        foreach ($seeds as $seed) {
//            \App\Resources::create($seed);
//        }
    }
}
