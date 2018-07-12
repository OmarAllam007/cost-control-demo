<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChangeOrderToPeriods extends Migration
{

    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->float('potential_change_order_amount')->nullable()->default(0);
            $table->float('change_order_amount')->nullable()->default(0);
            $table->integer('time_extension')->nullable()->default(0);
            $table->date('planned_finish_date')->nullable();
        });
    }


    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('potential_change_order_amount');
            $table->dropColumn('change_order_amount');
            $table->dropColumn('time_extension');
            $table->dropColumn('planned_finish_date');
        });
    }
}
