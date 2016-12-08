<?php

use App\Project;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectPolicyTest extends TestCase
{
    use DatabaseTransactions;

    function test_can_see_budget()
    {
        /** @var User $user */
        $user = factory(User::class)->create(['is_admin' => false]);
        /** @var Project $project */
        $project = factory(Project::class)->create();

        $project_user = $project->users()->sync([['user_id' => $user->id, 'budget' => 1]]);

        $policy = new \App\Policies\ProjectPolicy();
        $this->assertEquals(true, $policy->budget($user, $project));
    }
}
