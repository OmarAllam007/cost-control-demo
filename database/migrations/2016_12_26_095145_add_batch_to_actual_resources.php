<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchToActualResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->integer('batch_id');
        });

        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->integer('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });

        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->dropColumn('batch_id');
        });
    }
}
