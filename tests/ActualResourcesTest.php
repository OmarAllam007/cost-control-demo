<?php


class ActualResourcesTest extends TestCase
{

    public function testEquations()
    {

        /** @var \App\ActualResources $resource */
        $resource = \App\ActualResources::find(2);

        $this->assertEquals(10,$resource->prev_qty);
        $this->assertEquals(100, $resource->prev_cost);
        $this->assertEquals(2.40, $resource->total_updated_eqv);

    }

    public function testOne(){
        $resource = \App\ActualResources::find(2);
        $this->assertEquals(2.40, $resource->total_updated_eqv);
    }


}