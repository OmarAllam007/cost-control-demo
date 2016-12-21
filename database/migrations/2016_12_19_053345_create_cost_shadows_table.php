<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostShadowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("project_id")->unsigned();
            $table->integer("wbs_level_id")->unsigned();
            $table->integer("period_id")->unsigned();
            $table->integer("resource_id")->unsigned();
            $table->integer("breakdown_resource_id")->unsigned();
            $table->double("current_cost", 12, 2)->nullable();
            $table->double("current_qty", 12, 2)->nullable();
            $table->double("current_unit_price", 12, 2)->nullable();
            $table->double("previous_cost", 12, 2)->nullable();
            $table->double("previous_qty", 12, 2)->nullable();
            $table->double("to_date_cost", 12, 2)->nullable();
            $table->double("to_date_qty", 12, 2)->nullable();
            $table->double("previous_unit_price", 12, 2)->nullable();
            $table->double("to_date_unit_price", 12, 2)->nullable();
            $table->float("progress")->nullable();
            $table->double("allowable_ev_cost", 12, 2)->nullable();
            $table->double("allowable_var", 12, 2)->nullable();
            $table->double("bl_allowable_cost", 12, 2)->nullable();
            $table->double("bl_allowable_var", 12, 2)->nullable();
            $table->double("remaining_qty", 12, 2)->nullable();
            $table->double("remaining_cost", 12, 2)->nullable();
            $table->double("remaining_unit_price", 12, 2)->nullable();
            $table->double("completion_qty", 12, 2)->nullable();
            $table->double("completion_cost", 12, 2)->nullable();
            $table->double("completion_unit_price", 12, 2)->nullable();
            $table->double("qty_var", 12, 2)->nullable();
            $table->double("cost_var", 12, 2)->nullable();
            $table->double("unit_price_var", 12, 2)->nullable();
            $table->double("physical_unit", 12, 2)->nullable();
            $table->double("pw_index", 12, 2)->nullable();
            $table->double("cost_variance_to_date_due_unit_price", 12, 2)->nullable();
            $table->double("allowable_qty", 12, 2)->nullable();
            $table->double("cost_variance_remaining_due_unit_price", 12, 2)->nullable();
            $table->double("cost_variance_completion_due_unit_price", 12, 2)->nullable();
            $table->double("cost_variance_completion_due_qty", 12, 2)->nullable();
            $table->double("cost_variance_to_date_due_qty", 12, 2)->nullable();
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
        Schema::drop('cost_shadows');
    }
}
