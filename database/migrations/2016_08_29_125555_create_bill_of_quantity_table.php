<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillOfQuantityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boqs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wbs_id');
            $table->text('item')->nullable();
            $table->text('description')->nullable();
            $table->text('type')->nullable();
            $table->integer('unit_id');
            $table->integer('quantity');
            $table->double('dry_ur',12,2);
            $table->double('price_ur',12,2);
            $table->text('arabic_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('boqs');
    }
}
