<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStdActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('std_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('division_id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('id_partial')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('std_activities');
    }
}