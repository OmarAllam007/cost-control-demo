<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RecordsUserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function models_record_who_created_and_updated()
    {
        $user = factory('App\User')->create();

        $this->be($user);

        $project = factory('App\Project')->create();

        $this->assertEquals($user->id, $project->created_by);

        $project->update(['name' => 'A test new name']);

        $this->assertEquals($user->id, $project->updated_by);

        $this->seeInDatabase('projects', ['id' => $project->id, 'created_by' => $user->id, 'updated_by' => $user->id]);
    }
}
