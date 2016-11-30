<?php

use Illuminate\Database\Seeder;

class BreakDownResourceShadowSeeder extends Seeder
{

    public function run()
    {
        $resources = \App\BreakdownResource::all();
        foreach ($resources as $resource) {
            $formatter = new \App\Formatters\BreakdownResourceFormatter($resource);
            \App\BreakDownResourceShadow::create($formatter->toArray());
        }
    }
}
