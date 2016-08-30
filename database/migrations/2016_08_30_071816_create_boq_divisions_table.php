<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoqDivisionsTable extends Migration
{
    public function up()
    {
        Schema::create('boq_divisions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('parent_id');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('boq_divisions');
    }
}