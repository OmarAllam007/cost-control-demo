<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoqQsToBudgetShadow extends Migration
{
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->unsignedInteger('boq_qs_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->dropColumn('boq_qs_id');
        });
    }
}
