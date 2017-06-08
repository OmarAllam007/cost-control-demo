<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisionBreakdownResourceShadowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_breakdown_resource_shadows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('breakdown_resource_id')->nullable();
            $table->integer('revision_breakdown_resource_id');
            $table->integer('revision_id');
            $table->integer('project_id');
            $table->integer('wbs_id');
            $table->integer('breakdown_id');
            $table->integer('activity_id');
            $table->integer('resource_type_id');
            $table->string('template', 255);
            $table->string('activity', 255);
            $table->string('cost_account', 255);
            $table->double('eng_qty', 12, 2);
            $table->double('budget_qty', 12, 2);
            $table->double('resource_qty', 12, 2)->nullable();
            $table->double('resource_waste', 12, 2)->nullable();
            $table->string('resource_type', 255)->nullable();
            $table->string('resource_code', 255)->nullable();
            $table->string('resource_name', 255)->nullable();
            $table->double('unit_price', 12, 2);
            $table->string('measure_unit', 255);
            $table->double('budget_unit', 12, 2);
            $table->double('budget_cost', 12, 2);
            $table->double('boq_equivilant_rate', 12, 2);
            $table->double('labors_count', 12, 2);
            $table->double('productivity_output', 12, 2);
            $table->string('productivity_ref', 255);
            $table->string('remarks', 255)->nullable();
            $table->integer('resource_id')->nullable();
            $table->unsignedInteger('productivity_id');
            $table->unsignedInteger('unit_id');
            $table->unsignedInteger('template_id');
            $table->string('code', 255);
            $table->double('progress', 12, 2)->nullable();
            $table->string('status', 255)->nullable();
            $table->unsignedInteger('boq_id');
            $table->unsignedInteger('survey_id');
            $table->unsignedInteger('boq_wbs_id');
            $table->unsignedInteger('survey_wbs_id');

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::drop('revision_breakdown_resource_shadows');
    }
}
