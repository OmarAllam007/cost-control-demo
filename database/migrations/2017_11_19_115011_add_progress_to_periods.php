<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgressToPeriods extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->float('planned_progress')->default(0)->nullable();
            $table->float('actual_progress')->default(0)->nullable();
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('planned_progress');
            $table->dropColumn('actual_progress');
        });
    }
}
