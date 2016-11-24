<?php


class ActualResourcesTest extends TestCase
{

    public function testEquations()
    {

        /** @var \App\ActualResources $resource */
        $resource = \App\ActualResources::find(2);

//        $this->assertEquals(10,$resource->prev_qty);
//        $this->assertEquals(10, $resource->total_updated_qty);
        $this->assertEquals(10, $resource->p_w_index);

    }



}