<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRollupToBreakdowns extends Migration
{
    public function up()
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dateTime('rolled_up_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dropColumn('rolled_up_at');
        });
    }
}
