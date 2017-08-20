<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectResourceTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_project_resource_will_create_a_public_resource()
    {
        $project = factory('App\Project')->create();

        $resource = factory(App\Resources::class)->create(['project_id' => $project->id]);

        $this->assertNotNull($resource->fresh()->resource_id);
    }
}
