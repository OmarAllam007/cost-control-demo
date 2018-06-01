<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWasteIndicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waste_indices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->index();
            $table->integer('period_id')->index();
            $table->integer('breakdown_resource_id');
            $table->integer('resource_id')->index();
            $table->integer('resource_type_id')->index();
            $table->float('to_date_unit_price', 14, 4);
            $table->float('to_date_qty', 14, 4);
            $table->float('allowable_qty', 14, 4);
            $table->float('qty_var', 14, 4);
            $table->float('waste_var', 14, 4);
            $table->float('waste_index', 14, 4);
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
        Schema::drop('waste_indices');
    }
}
