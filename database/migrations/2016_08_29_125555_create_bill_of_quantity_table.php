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
            $table->text('item');
            $table->text('description');

            $table->text('type')->nullable();
            $table->integer('unit_id');
            $table->integer('quantity');
            $table->float('dry_ur');
            $table->float('price_ur');
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
