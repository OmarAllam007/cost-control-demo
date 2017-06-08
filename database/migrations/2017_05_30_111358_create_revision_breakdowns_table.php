<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisionBreakdownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_breakdowns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('breakdown_id')->nullable();
            $table->unsignedInteger('revision_id');
            $table->unsignedInteger('wbs_level_id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('template_id');
            $table->unsignedInteger('std_activity_id');
            $table->string('cost_account', 255);
            $table->string('code', 255)->nullable();
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
        Schema::drop('revision_breakdowns');
    }
}
