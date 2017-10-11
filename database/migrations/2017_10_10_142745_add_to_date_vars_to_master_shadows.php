<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToDateVarsToMasterShadows extends Migration
{
    public function up()
    {
        Schema::table('master_shadows', function (Blueprint $table) {
            $table->float('to_date_price_var', 16, 2)->nullable();
            $table->float('to_date_qty_var', 16, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('master_shadows', function (Blueprint $table) {
            $table->dropColumn('to_date_price_var', 16, 2);
            $table->dropColumn('to_date_qty_var', 16, 2);
        });
    }
}
