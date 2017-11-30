<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQsToBreakdown extends Migration
{
    public function up()
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->string('qs_code')->nullable();
            $table->unsignedInteger('qs_id')->nullable();
            $table->unsignedInteger('boq_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dropColumn('qs_code');
            $table->dropColumn('qs_id');
            $table->dropColumn('boq_id');
        });
    }
}
