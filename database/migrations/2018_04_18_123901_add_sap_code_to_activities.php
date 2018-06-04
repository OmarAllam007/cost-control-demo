<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapCodeToActivities extends Migration
{
    public function up()
    {
        Schema::table('std_activities', function (Blueprint $table) {
            $table->string('sap_code_partial');
        });
    }

    public function down()
    {
        Schema::table('std_activities', function (Blueprint $table) {
            $table->dropColumn('sap_code_partial');
        });
    }
}
