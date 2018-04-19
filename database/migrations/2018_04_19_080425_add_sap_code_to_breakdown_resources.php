<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapCodeToBreakdownResources extends Migration
{
    public function up()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->string('sap_code');
        });
    }

    public function down()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->dropColumn('sap_code');
        });
    }
}
