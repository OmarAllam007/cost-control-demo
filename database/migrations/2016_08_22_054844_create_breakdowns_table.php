<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakdownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breakdowns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wbs_level_id')->unsigned();
            $table->integer('project_id')->unsigned();
            $table->integer('template_id')->unsigned();
            $table->integer('std_activity_id')->unsigned();
            $table->string('cost_account');
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
        Schema::drop('breakdowns');
    }
}
