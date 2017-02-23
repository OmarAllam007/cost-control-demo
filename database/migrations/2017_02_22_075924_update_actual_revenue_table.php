<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActualRevenueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actual_revenue', function (Blueprint $table) {
           $table->integer('wbs_id');
           $table->integer('period_id');
           $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actual_revenue', function (Blueprint $table) {
            $table->dropColumn('wbs_id');
            $table->dropColumn('period_id');
            $table->dropColumn('quantity');
        });
    }
}
