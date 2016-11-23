<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakDownResourceShadowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_down_resource_shadows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('breakdown_resource_id')->unsigned();
            $table->integer('project_id')->unsigned();
            $table->integer('wbs_id')->unsigned();
            $table->integer('breakdown_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->integer('resource_id')->unsigned();
            $table->integer('resource_type_id')->unsigned();
            $table->string('template');
            $table->string('activity');
            $table->string('cost_account');
            $table->float('eng_qty');
            $table->float('budget_qty');
            $table->float('resource_qty')->nullable();
            $table->float('resource_waste')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('resource_code')->nullable();
            $table->string('resource_name')->nullable();
            $table->float('unit_price');
            $table->string('measure_unit');
            $table->float('budget_unit');
            $table->float('budget_cost');
            $table->float('boq_equivilant_rate');
            $table->float('labors_count');
            $table->float('productivity_output');
            $table->string('productivity_ref');
            $table->string('remarks')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('break_down_resource_shadows');
    }
}
