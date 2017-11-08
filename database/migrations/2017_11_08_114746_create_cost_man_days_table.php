<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostManDaysTable extends Migration
{
    public function up()
    {
        Schema::create('cost_man_days', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('period_id');
            $table->unsignedInteger('wbs_id');
            $table->unsignedInteger('activity_id');
            $table->float('progress');
            $table->float('actual');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('cost_man_days');
    }
}
