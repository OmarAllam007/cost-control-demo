<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGlobalPeriodToRevisions extends Migration
{
    public function up()
    {
        Schema::table('budget_revisions', function (Blueprint $table) {
            $table->unsignedInteger('global_period_id');
        });
    }

    public function down()
    {
        Schema::table('budget_revisions', function (Blueprint $table) {
            $table->dropColumn('global_period_id');
        });
    }
}
