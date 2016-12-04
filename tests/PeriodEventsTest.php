<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PeriodEventsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function first_period_is_made_open()
    {
        $project = factory(\App\Project::class)->create();
        $period = $project->periods()->save(factory(App\Period::class)->make());

        $this->assertTrue($period->is_open);
    }

    /** @test */
    function making_period_open_disabled_all_others_in_project()
    {
        $project = factory(\App\Project::class)->create();
        $firstPeriod = $project->periods()->save(factory(App\Period::class)->make());
        $project->periods()->save(factory(App\Period::class)->make());
        $middlePeriod = $project->periods()->save(factory(App\Period::class)->make());
        $lastPeriod = $project->periods()->save(factory(App\Period::class)->make());

        $this->assertTrue($firstPeriod->is_open);

        $middlePeriod->is_open = true;
        $middlePeriod->save();

//        $this->assertFalse($firstPeriod->fresh()->is_open);
        $this->assertCount(3, $project->periods()->where('is_open', false)->get());
        $this->assertCount(1, $project->periods()->where('is_open', true)->get());
    }
}
