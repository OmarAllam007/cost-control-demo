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
            $table->softDeletes();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::drop('resource_types');
    }
}