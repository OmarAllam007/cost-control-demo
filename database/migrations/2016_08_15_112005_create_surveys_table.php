<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveysTable extends Migration
{
    public function up()
    {
        Schema::create('qty_surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->string('description');
            $table->integer('unit_id')->unsigned();
            $table->double('budget_qty',12,2);
            $table->double('eng_qty',12,2);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
       Schema::drop('qty_surveys');
    }
}