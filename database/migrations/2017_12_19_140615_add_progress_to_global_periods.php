<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgressToGlobalPeriods extends Migration
{
    public function up()
    {
        Schema::table('global_periods', function (Blueprint $table) {
            $table->float('actual_progress')->nullable();
        });
    }

    
    public function down()
    {
        Schema::table('global_periods', function (Blueprint $table) {
            $table->dropColumn('actual_progress');
        });
    }
}
