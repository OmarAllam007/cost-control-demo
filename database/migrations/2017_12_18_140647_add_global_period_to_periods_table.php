<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGlobalPeriodToPeriodsTable extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->unsignedInteger('global_period_id');
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('global_period_id');
        });
    }
}
