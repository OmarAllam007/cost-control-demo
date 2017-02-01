<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('productivities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('csi_category_id');
            $table->string('unit');
            $table->string('description')->nullable();
            $table->string('crew_structure');
            $table->double('crew_hours',12,2);
            $table->double('crew_equip',12,2);
            $table->double('daily_output',12,2);
            $table->double('man_hours',12,2);
            $table->double('equip_hours',12,2);
            $table->double('reduction_factor',12,2);
            $table->double('after_reduction',12,2);
            $table->string('source',12,2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('productivities');
    }
}