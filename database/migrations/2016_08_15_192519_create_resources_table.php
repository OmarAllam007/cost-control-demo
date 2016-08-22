<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resource_type_id');
            $table->string('resource_code');
            $table->string('name');
            $table->float('rate');
            $table->string('unit');
            $table->float('waste');
            $table->string('reference');
            $table->integer('business_partner_id')->unsigned();
//            $table->foreign('business_partner_id')->references('id')->on('business_partners');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('resources');
    }
}