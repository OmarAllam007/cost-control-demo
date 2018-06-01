<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapCodeToBreakdowns extends Migration
{
    public function up()
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->string('sap_code');
        });
    }

    public function down()
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dropColumn('sap_code');
        });
    }
}
