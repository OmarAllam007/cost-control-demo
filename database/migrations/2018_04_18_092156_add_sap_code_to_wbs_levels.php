<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapCodeToWbsLevels extends Migration
{
    public function up()
    {
        Schema::table('wbs_levels', function (Blueprint $table) {
            $table->string('sap_code');
        });
    }

    public function down()
    {
        Schema::table('wbs_levels', function (Blueprint $table) {
            $table->dropColumn('sap_code');
        });
    }
}
