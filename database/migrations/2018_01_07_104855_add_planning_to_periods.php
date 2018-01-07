<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlanningToPeriods extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            //planned cost value and & EV & Actual invoice amount
            $table->float('planned_cost', 12, 2)->nullable();
            $table->float('earned_value', 12, 2)->nullable();
            $table->float('actual_invoice_amount', 12, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('planned_cost');
            $table->dropColumn('earned_value');
            $table->dropColumn('actual_invoice_amount');
        });
    }
}
