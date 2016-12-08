<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShadowTes extends TestCase
{
    public function testBasicExample()
    {

        $breakdownResource = \App\BreakDownResourceShadow::where('resource_id', 9898)->first();
        $oldCost =$breakdownResource->budgetCost;

        $resource = \App\Resources::where('id',9898)->first();
        $resource->rate = $resource->rate + 1;
        $resource->save();

        $newBreakdownResource = \App\BreakDownResourceShadow::find($breakdownResource->id);
        $newCost = $newBreakdownResource->budget_cost;
        $this->assertEquals($newCost,$oldCost);
    }
}
