<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisionBreakdownResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_breakdown_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('breakdown_resource_id')->nullable();
            $table->integer('revision_id');
            $table->integer('breakdown_id');
            $table->integer('std_activity_resource_id');
            $table->double('budget_qty', 12, 2);
            $table->double('eng_qty', 12, 2);
            $table->string('remarks', 255)->nullable();
            $table->float('labor_count')->nullable();
            $table->unsignedInteger('productivity_id')->nullable();
            $table->double('resource_waste', 12, 2)->nullable();
            $table->string('code', 255)->nullable();
            $table->double('resource_qty', 12, 2)->nullable();
            $table->tinyInteger('resource_qty_manual')->nullable();
            $table->unsignedInteger('resource_id')->nullable();
            $table->string('equation', 255)->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
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
        Schema::drop('revision_breakdown_resources');
    }
}
