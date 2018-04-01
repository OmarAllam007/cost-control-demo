<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualProdIndexToPeriod extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->float('productivity_index',10, 4)->nullable()->default(0);
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('productivity_index');
        });
    }
}
