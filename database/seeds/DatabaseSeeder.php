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
        $this->call(ProjectSeed::class);
        $this->call(ProductivitySeed::class);
        $this->call(BusinessPartnerSeed::class);
        $this->call(ActivityDivisionSeeder::class);
        $this->call(WBSLevelSeeder::class);
        $this->call(ProductivityListSeed::class);
        $this->call(BreakDownResourceShadowSeeder::class);
        $this->call(BreakdownResourcesSeed::class);
    }

}
