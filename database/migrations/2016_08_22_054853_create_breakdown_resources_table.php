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
            $table->double('budget_qty',12,2);
            $table->double('eng_qty',12,2);
            $table->string('remarks')->nullable();
            $table->double('labor_count',12,2)->nullable();
            $table->integer('productivity_id')->unsigned()->nullable();
            $table->double('resource_waste',12,2)->nullable();
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
