<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductivitiesTable extends Migration
{
    public function up()
    {
        Schema::table('productivities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('csi');
            $table->string('description');
            $table->string('unit');
            $table->string('crew_structure');
            $table->float('crew_hours');
            $table->float('crew_equip');
            $table->float('daily_output');
            $table->float('man_hours');
            $table->float('equip_hours');
            $table->float('reduction_factor');
            $table->float('after_reduction');
            $table->string('source');


            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('productivities');
    }
}