<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->float('spi_index')->default(0)->nullable();
            $table->float('planned_progress')->default(0)->nullable();
            $table->float('actual_progress')->default(0)->nullable();
            $table->boolean('is_closed')->nullable();
            $table->integer('time_elapsed')->nullable();
            $table->integer('time_remaining')->nullable();
            $table->integer('expected_duration')->nullable();
            $table->integer('duration_variance')->nullable();
            $table->float('change_order_amount')->nullable()->default(0);
            $table->integer('time_extension')->nullable()->default(0);
            $table->date('planned_finish_date')->nullable();
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
        Schema::drop('global_periods');
    }
}
