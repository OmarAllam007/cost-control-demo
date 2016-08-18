<?php

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ResourceTypeSeed::class);
        $this->call(ResourceSeed::class);
        $this->call(UnitSeed::class);
        $this->call(SurveyCategorySeed::class);
        $this->call(CSICategorySeed::class);
    }
}
