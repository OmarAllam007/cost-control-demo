<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveysTable extends Migration
{
    public function up()
    {
//        Schema::create('qty_surveys', function (Blueprint $table) {
//            $table->increments('id');
//            $table->integer('unit_id');
//            $table->float('budget_qty');
//            $table->float('eng_qty');
//            $table->foreign('unit_id')->references('id')->on('units');
//            $table->softDeletes();
//            $table->timestamps();
//        });
    }

    public function down()
    {
//        Schema::drop('surveys');
    }
}