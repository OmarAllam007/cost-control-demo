<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterShadowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_shadows', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('period_id');
            $table->unsignedInteger('budget_id');
            $table->unsignedInteger('breakdown_resource_id');
            $table->unsignedInteger('wbs_id');
            $table->unsignedInteger('activity_id');
            $table->unsignedInteger('resource_id');
            $table->unsignedInteger('resource_type_id');
            $table->unsignedInteger('productivity_id');
            $table->text('wbs');
            $table->text('activity_divs');
            $table->text('resource_divs');
            $table->string('activity');
            $table->string('code');
            $table->string('boq');
            $table->string('cost_account');
            $table->double('eng_qty');
            $table->double('budget_qty');
            $table->double('resource_qty');
            $table->double('waste');
            $table->string('resource_code');
            $table->string('resource_name');
            $table->string('top_material');
            $table->double('unit_price');
            $table->string('measure_unit');
            $table->double('budget_unit');
            $table->double('budget_cost');
            $table->double('boq_equivilant_rate');
            $table->double('budget_unit_rate');
            $table->double('labors_count');
            $table->double('productivity_output');
            $table->string('productivity_ref');
            $table->string('remarks');
            $table->double('progress');
            $table->string('status');
            $table->double('prev_unit_price', 18, 6);
            $table->double('prev_qty', 18, 6);
            $table->double('prev_cost', 18, 6);
            $table->double('curr_unit_price', 18, 6);
            $table->double('curr_qty', 18, 6);
            $table->double('curr_cost', 18, 6);
            $table->double('to_date_unit_price', 18, 6);
            $table->double('to_date_qty', 18, 6);
            $table->double('to_date_cost', 18, 6);
            $table->double('allowable_ev_cost', 18, 6);
            $table->double('allowable_var', 18, 6);
            $table->double('remaining_unit_price', 18, 6);
            $table->double('remaining_qty', 18, 6);
            $table->double('remaining_cost', 18, 6);
            $table->double('bl_allowable_cost', 18, 6);
            $table->double('bl_allowable_var', 18, 6);
            $table->double('completion_unit_price', 18, 6);
            $table->double('completion_qty', 18, 6);
            $table->double('completion_cost', 18, 6);
            $table->double('unit_price_var', 18, 6);
            $table->double('qty_var', 18, 6);
            $table->double('cost_var', 18, 6);
            $table->double('physical_unit', 18, 6);
            $table->double('cost_variance_to_date_due_unit_price', 18, 6);
            $table->double('allowable_qty', 18, 6);
            $table->double('pw_index', 18, 6)->nullable();
            $table->double('cost_variance_remaining_due_unit_price', 18, 6);
            $table->double('cost_variance_completion_due_unit_price', 18, 6);
            $table->double('cost_variance_completion_due_qty', 18, 6);
            $table->double('cost_variance_to_date_due_qty', 18, 6);
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
        Schema::drop('master_shadows');
    }
}
