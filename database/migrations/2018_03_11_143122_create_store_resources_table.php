<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('store_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('period_id');
            $table->unsignedInteger('batch_id');
            $table->string('budget_code')->nullable();
            $table->unsignedInteger('resource_id')->nullable();
            $table->string('activity_code');
            $table->date('store_date');
            $table->string('item_code');
            $table->string('item_desc');
            $table->string('measure_unit');
            $table->float('unit_price', 12, 2);
            $table->float('qty', 12, 2);
            $table->float('cost', 12, 2);
            $table->text('doc_no')->nullable();
            $table->text('row_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('store_resources');
    }
}
