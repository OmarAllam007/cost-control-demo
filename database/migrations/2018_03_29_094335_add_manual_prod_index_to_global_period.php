<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualProdIndexToGlobalPeriod extends Migration
{
    public function up()
    {
        Schema::table('global_periods', function (Blueprint $table) {
            $table->float('productivity_index', 10, 4)->nullable()->default(0);
        });
    }

    public function down()
    {
        Schema::table('global_periods', function (Blueprint $table) {
            $table->dropColumn('productivity_index');
        });
    }
}
