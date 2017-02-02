<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBreakdownResourceCodeToShadow extends Migration
{
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
    }

    public function down()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
}
