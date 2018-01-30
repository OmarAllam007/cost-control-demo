<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRollupToBreakdownResourceShadows extends Migration
{

    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->boolean('show_in_budget')->nullable()->default(0);
            $table->boolean('show_in_cost')->nullable()->default(0);
            $table->dateTime('rolled_up_at')->nullable();
            $table->boolean('is_rollup')->nullable()->default(0);
            $table->unsignedInteger('rollup_resource_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->dropColumn('show_in_budget');
            $table->dropColumn('show_in_cost');
            $table->dropColumn('rolled_up_at');
            $table->dropColumn('is_rollup');
            $table->dropColumn('rollup_resource_id');
        });
    }
}
