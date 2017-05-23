<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CostAttributesTest extends TestCase
{
//    use DatabaseTransactions;

    /** @var \App\Project */
    protected $project;

    /** @var \App\WbsLevel */
    protected $wbs;

    protected function setUp()
    {
        parent::setUp();

        $this->project = factory('App\Project')->create();
        $this->wbs = factory(App\WbsLevel::class)->create();
    }

    public function test_material_remaining_unit_price_with_current()
    {
        $qtySurvey = factory(App\Survey::class)->create(['project_id' => $this->project->id, 'wbs_level_id' => $this->wbs->id]);
        $resourceType = factory(App\ResourceType::class)->create(['name' => '03. Material']);
        $resource = factory(App\Resources::class)->create(['resource_type_id' => $resourceType->id]);
        $template = factory(App\BreakdownTemplate::class)->create();
        $stdResource = factory(App\StdActivityResource::class)->create(['template_id' => $template->id, 'resource_id' => $resource->id]);

        $breakdown = factory(App\Breakdown::class)->create([
            'project_id' => $this->project->id, 'wbs_level_id' => $this->wbs->id, 'cost_account' => $qtySurvey->cost_account,

        ]);

        $breakdownResource = factory(App\BreakdownResource::class)->create(['breakdown_id' => $breakdown->id, 'std_activity_resource_id' => $stdResource->id]);

        $period = factory(App\Period::class)->create(['project_id' => $this->project->id]);

        $actuals = factory(App\ActualResources::class, 2)->create([
            'breakdown_resource_id' => $breakdownResource->id, 'resource_id' => $resource->id,
            'project_id' => $this->project->id, 'wbs_level_id' => $this->wbs->id, 'period_id' => $period->id
        ]);


        $unit_price = round($actuals->sum('cost') / $actuals->sum('qty'), 2);

        $shadow = \App\BreakDownResourceShadow::whereBreakdownResourceId($breakdownResource->id)->first();

        $costShadow = \App\CostShadow::whereBreakdownResourceId($breakdownResource->id)->first();

        $this->assertEquals($unit_price, round($shadow->latest_remaining_unit_price, 2));
        $this->assertEquals($unit_price, round($costShadow->latest_remaining_unit_price, 2));
    }

    public function test_material_remaining_unit_price_with_todate()
    {
        $qtySurvey = factory(App\Survey::class)->create(['project_id' => $this->project->id, 'wbs_level_id' => $this->wbs->id]);
        $resourceType = factory(App\ResourceType::class)->create(['name' => '03. Material']);
        $resource = factory(App\Resources::class)->create(['resource_type_id' => $resourceType->id]);
        $template = factory(App\BreakdownTemplate::class)->create();
        $stdResource = factory(App\StdActivityResource::class)->create(['template_id' => $template->id, 'resource_id' => $resource->id]);

        $breakdown = factory(App\Breakdown::class)->create([
            'project_id' => $this->project->id, 'wbs_level_id' => $this->wbs->id, 'cost_account' => $qtySurvey->cost_account,

        ]);

        $breakdownResource = factory(App\BreakdownResource::class)->create(['breakdown_id' => $breakdown->id, 'std_activity_resource_id' => $stdResource->id]);

        $period1 = factory(App\Period::class)->create(['project_id' => $this->project->id]);

        $actuals = factory(App\ActualResources::class, 2)->create([
            'breakdown_resource_id' => $breakdownResource->id, 'resource_id' => $breakdownResource->resource->id,
            'project_id' => $breakdownResource->breakdown->project_id,
            'wbs_level_id' => $breakdownResource->breakdown->wbs_level_id, 'period_id' => $period1->id
        ]);

        $unit_price = round($actuals->sum('cost') / $actuals->sum('qty'), 2);

        $period2 = factory(App\Period::class)->create(['project_id' => $this->project->id]);

        $shadow = \App\BreakDownResourceShadow::whereBreakdownResourceId($breakdownResource->id)->first();


        $this->assertEquals($unit_price, round($shadow->latest_remaining_unit_price, 2));

//        $costShadow = \App\CostShadow::whereBreakdownResourceId($breakdownResource->id)->first();
//        $this->assertEquals($unit_price, round($costShadow->latest_remaining_unit_price, 2));
    }

    /* public function test_not_started_material_remaining_unit_price()
    {

    }

    public function test_other_remaining_unit_price_with_current()
    {

    }

    public function test_other_remaining_unit_price_with_todate()
    {

    }

    public function test_not_started_other_remaining_unit_price()
    {

    }*/
}
