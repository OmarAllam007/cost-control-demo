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
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('open');
            $table->dateTime('opened_time');
            $table->dateTime('closed_time');
            $table->text('description');
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
