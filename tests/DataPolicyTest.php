<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DataPolicyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    function can_read()
    {
        /** @var User $user */
        $user = factory(User::class)->create(['is_admin' => false]);
        $module = \App\Module::first();

        $user->modules()->sync([['module_id' => $module->id, 'read' => true]]);

        $policy = new \App\Policies\DataPolicy($user);
        $this->assertTrue($policy->read($user, $module->id));
    }

    /** @test */
    function cannot_read()
    {
        /** @var User $user */
        $user = factory(User::class)->create(['is_admin' => false]);
        $module = \App\Module::first();

        $user->modules()->sync([['module_id' => $module->id, 'read' => false]]);

        $policy = new \App\Policies\DataPolicy($user);
        $this->assertFalse($policy->read($user, $module->id));
    }
}
