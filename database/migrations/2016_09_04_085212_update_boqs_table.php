<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBoqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boqs', function (Blueprint $table) {
            $table->string('item_code');
            $table->string('cost_account');
            $table->float('kcc_qty')->nullable();
            $table->float('subcon')->nullable();
            $table->float('materials')->nullable();
            $table->float('manpower')->nullable();
            $table->integer('project_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boqs', function (Blueprint $table) {
//            $table->dropColumn('item_code');
//            $table->dropColumn('cost_account');
//            $table->dropColumn('kcc_qty');
//            $table->dropColumn('subcon');
//            $table->dropColumn('materials');
//            $table->dropColumn('manpower');
        });
    }
}
