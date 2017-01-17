<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectAndPeriodToBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actual_batches', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->integer('period_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actual_batches', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('period_id');
        });
    }
}
