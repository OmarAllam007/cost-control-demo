<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakdownResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breakdown_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('breakdown_id');
            $table->integer('std_activity_resource_id');
            $table->float('budget_qty');
            $table->float('eng_qty');
            $table->integer('remarks');
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
        Schema::drop('breakdown_resources');
    }
}
