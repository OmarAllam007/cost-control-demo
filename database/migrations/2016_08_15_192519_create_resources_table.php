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
            $table->double('rate',12,2);
            $table->string('unit');
            $table->double('waste',12,2)->nullable();
            $table->string('reference')->nullable();
            $table->integer('business_partner_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('resources');
    }
}