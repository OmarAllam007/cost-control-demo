<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgressAndStatusToActualResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('actual_resources', function (Blueprint $table) {
            $table->float('progress')->nullable();
            $table->string('status')->nullable();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('actual_resources', function (Blueprint $table) {
            $table->dropColumn('progress');
            $table->dropColumn('status');
        });*/
    }
}
