<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceTypesTable extends Migration
{
    public function up()
    {
        Schema::create('resource_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('parent_id')->unsigned();
            $table->integer('resource_id');

            $table->foreign('parent_id')->references('id')->on('resource_types');
            $table->foreign('resource_id')->references('id')->on('resources');


            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('resource_types');
    }
}