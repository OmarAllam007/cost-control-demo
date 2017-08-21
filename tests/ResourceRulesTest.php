<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ResourceRulesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_project_resource_will_create_a_public_resource()
    {
        $project = factory('App\Project')->create();

        $resource = factory(App\Resources::class)->create(['project_id' => $project->id]);

        $this->assertNotNull($resource->fresh()->resource_id);
    }

    /** @test */
    function resource_code_follows_code_pattern()
    {
        $type = factory('App\ResourceType')->create();
        $subtype1 = factory('App\ResourceType')->create(['parent_id' => $type->id, 'code' => '']);
        $subtype2 = factory('App\ResourceType')->create(['parent_id' => $subtype1->id, 'code' => '']);

        $resource1 = factory('App\Resources')->create(['resource_type_id' => $subtype2->id]);
        $resource2 = factory('App\Resources')->create(['resource_type_id' => $subtype2->id]);

        $code1 = $subtype2->code . '.' . '001';
        $code2 = $subtype2->code . '.' . '002';

        $this->assertEquals($code1, $resource1->resource_code);
        $this->assertEquals($code2, $resource2->resource_code);
    }
}
