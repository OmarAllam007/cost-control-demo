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
            $table->double('eng_qty',12,2);
            $table->double('budget_qty',12,2);
            $table->double('resource_qty',12,2)->nullable();
            $table->double('resource_waste',12,2)->nullable();
            $table->string('resource_type')->nullable();
            $table->string('resource_code')->nullable();
            $table->string('resource_name')->nullable();
            $table->double('unit_price',12,2);
            $table->string('measure_unit');
            $table->double('budget_unit',12,2);
            $table->double('budget_cost',12,2);
            $table->double('boq_equivilant_rate',12,2);
            $table->double('labors_count',12,2);
            $table->double('productivity_output',12,2)->nullable();
            $table->string('productivity_ref')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('productivity_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->integer('template_id')->unsigned();
            $table->double('progress',12,2)->nullable();
            $table->string('status')->nullable();
            $table->unsignedInteger('boq_id');
            $table->unsignedInteger('survey_id');
            $table->unsignedInteger('boq_wbs_id');
            $table->unsignedInteger('survey_wbs_id');
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
