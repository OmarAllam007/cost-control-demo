<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    public function up()
    {
        Schema::table('resources', function (Blueprint $table) {
//            $table->increments('id');
//            $table->string('resource_code');
//            $table->string('name');
//            $table->float('rate');
//            $table->string('unit');
//            $table->float('waste');
//            $table->integer('business_partner')->unsigned();
//            $table->foreign('business_partner')->references('id')->on('business_partners');
//
//
//
//            $table->softDeletes();
//            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('resources');
    }
}