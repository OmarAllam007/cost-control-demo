<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSapCodeToBreakdownResourceShadows extends Migration
{
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->string('sap_code');
        });
    }

    public function down()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->dropColumn('sap_code');
        });
    }
}
