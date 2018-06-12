<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportantActualResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('important_actual_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('wbs_level_id')->unsigned();
            $table->integer('breakdown_resource_id')->unsigned();
            $table->integer('period_id')->unsigned();
            $table->string('original_code')->nullable();
            $table->double('qty',12,2)->unsigned();
            $table->double('unit_price',12,2)->unsigned();
            $table->double('cost', 12, 2)->unsigned();
            $table->double('unit_id', 12, 2)->unsigned();
            $table->date('action_date')->nullable();
            $table->integer('resource_id')->unsigned();
            $table->integer('user_id')->undigned();
            $table->integer('batch_id');
            $table->string('doc_no')->nullable();
            $table->text('original_data')->nullable();
            $table->float('progress')->nullable();
            $table->string('status')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::drop('important_actual_resources');
    }
}