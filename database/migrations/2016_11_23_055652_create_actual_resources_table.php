<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActualResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actual_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('wbs_level_id')->unsigned();
            $table->integer('breakdown_resource_id')->unsigned();
            $table->integer('period_id')->unsigned();
            $table->string('original_code')->nullable();
            $table->float('qty')->unsigned();
            $table->float('unit_price')->unsigned();
            $table->float('cost', 11, 2)->unsigned();
            $table->float('unit_id', 11, 2)->unsigned();
            $table->date('action_date')->nullable();
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
        Schema::drop('actual_resources');
    }
}
