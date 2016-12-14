<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActualResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->integer('resource_id')->unsigned();
            $table->integer('user_id')->undigned();
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
            $table->dropColumn('resource_id');
            $table->dropColumn('user_id');
        });
    }
}
