<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancialPeriodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('open');
            $table->date('last_open_date')->nullable();
            $table->dateTime('last_close_date')->nullable();
            $table->text('description')->nullable();
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
        Schema::drop('financial_periods');
    }
}
