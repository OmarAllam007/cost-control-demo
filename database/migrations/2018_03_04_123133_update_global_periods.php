<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateGlobalPeriods extends Migration
{
    public function up()
    {
        Schema::table('global_periods', function (Blueprint $table) {
            $table->double('planned_value', 14, 2);
            $table->double('earned_value', 14, 2);
            $table->double('actual_invoice_value', 14, 2);
        });
    }

    public function down()
    {
        Schema::table('global_periods', function (Blueprint $table) {
            $table->dropColumn('planned_value');
            $table->dropColumn('earned_value');
            $table->dropColumn('actual_invoice_value');
        });
    }
}
