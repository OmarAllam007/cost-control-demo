<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakdownVariablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breakdown_variables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('breakdown_resource_id');
            $table->integer('qty_survey_id');
            $table->string('name');
            $table->float('value');
            $table->integer('display_order');
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
        Schema::drop('breakdown_variables');
    }
}
