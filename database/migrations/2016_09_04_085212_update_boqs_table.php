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
            $table->double('kcc_qty',12,2)->nullable();
            $table->double('subcon',12,2)->nullable();
            $table->double('materials',12,2)->nullable();
            $table->double('manpower',12,2)->nullable();
            $table->integer('project_id',12,2);
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
