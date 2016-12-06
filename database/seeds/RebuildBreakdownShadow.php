<?php

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Formatters\BreakdownResourceFormatter;
use Illuminate\Database\Seeder;

class RebuildBreakdownShadow extends Seeder
{
    public function run()
    {
        BreakDownResourceShadow::truncate();

        set_time_limit(1800);

        $resources = BreakdownResource::all();
        foreach ($resources as $resource) {
            $formatter = new BreakdownResourceFormatter($resource);
            BreakDownResourceShadow::create($formatter);
        }
    }
}
