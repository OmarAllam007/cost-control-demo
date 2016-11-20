<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakDownResourceShadowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_down_resource_shadows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('breakdown_id');
            $table->integer('std_activity_resource_id');
            $table->float('budget_qty');
            $table->float('eng_qty');
            $table->string('remarks')->nullable();
            $table->float('labor_count')->nullable();
            $table->integer('productivity_id')->unsigned()->nullable();
            $table->float('resource_waste')->nullable();
            $table->timestamps();
            $table->string('code')->nullable();
            $table->float('resource_qty')->nullable();
            $table->tinyInteger('resource_qty_manual')->nullable();
            $table->integer('resource_id')->nullable();
            $table->string('equation')->nullable();
            $table->integer('project_id')->nullable();
            $table->integer('wbs_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('break_down_resource_shadows');
    }
}
