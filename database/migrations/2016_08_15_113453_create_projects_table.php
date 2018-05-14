<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('owner_id')->unsigned();
            $table->string('description')->nullable();
            $table->text('project_code')->nullable();
            $table->text('client_name')->nullable();
            $table->text('project_location')->nullable();
            $table->text('project_contract_value')->nullable();
            $table->date('project_start_date')->nullable();
            $table->text('project_duration')->nullable();
            $table->date('original_finished_date')->nullable();
            $table->date('expected_finished_date')->nullable();
            $table->text('project_contract_signed_value',15,2)->nullable();
            $table->text('project_contract_budget_value',15,2)->nullable();
            $table->text('change_order_amount',15,2)->nullable();
            $table->text('direct_cost_material',15,2)->nullable();
            $table->text('indirect_cost_general',15,2)->nullable();
            $table->text('total_budget_cost',15,2)->nullable();
            $table->boolean('is_activity_rollup')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('projects');
    }
}