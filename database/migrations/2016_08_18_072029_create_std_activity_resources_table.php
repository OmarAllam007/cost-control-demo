<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStdActivityResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('std_activity_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('template_id')->unsigned();
            $table->integer('resource_id')->unsigned();
            $table->string('equation');
            $table->double('default_value',12,2)->nullable();
            $table->boolean('allow_override')->default(0);
            $table->integer('project_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('std_activity_resources');
    }
}