<?php

use App\Project;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BuiltAreaCalcTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    public function test_site_work_cost_per_m2()
    {
        $project = factory(Project::class)->create();
        $project->sw_area = 100;
        $project->sw_cost = 4000;

        $this->assertEquals(40, $project->sw_cost_per_m2);
    }

    public function test_building_area_cost_per_m2()
    {
        $project = factory(Project::class)->create();
        $project->building_area = 100;
        $project->building_cost = 4000;

        $this->assertEquals(40, $project->building_cost_per_m2);
    }

    function test_built_area_price_per_m2()
    {
        $project = factory(Project::class)->create();
        $project->cached_eac_contract_amount = 10000;
        $project->building_area = 100;
        $project->building_cost = 4000;
        $project->sw_area = 100;
        $project->sw_cost = 4000;

        $this->assertEquals(10000/200, $project->built_price_per_m2);
    }

    function test_total_built_area_cost_per_m2()
    {
        $project = factory(Project::class)->create();
        $project->cached_budget_cost = 10000;
        $project->building_area = 100;
        $project->building_cost = 4000;
        $project->sw_area = 100;
        $project->sw_cost = 4000;

        $this->assertEquals(10000/200, $project->total_built_cost_per_m2);
    }
}
