<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('project_id');
            $table->boolean('budget');
            $table->boolean('wbs');
            $table->boolean('breakdown');
            $table->boolean('breakdown_templates');
            $table->boolean('resources');
            $table->boolean('productivity');
            $table->boolean('reports');
            $table->boolean('cost_control');
            $table->boolean('actual_resources');
            $table->boolean('boq');
            $table->boolean('qty_survey');
            $table->boolean('activity_mapping')->nullable();
            $table->boolean('resource_mapping')->nullable();
            $table->boolean('periods')->nullable();
            $table->boolean('remaining_unit_price')->nullable();
            $table->boolean('remaining_unit_qty')->nullable();
            $table->boolean('manual_edit')->nullable();
            $table->boolean('delete_resources')->nullable();
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
        Schema::drop('project_users');
    }
}
