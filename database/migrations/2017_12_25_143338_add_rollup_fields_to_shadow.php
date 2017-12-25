<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRollupFieldsToShadow extends Migration
{
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->boolean('show_in_budget')->nullable()->default(1);
            $table->boolean('show_in_cost')->nullable()->default(1);
            $table->boolean('is_rolled_up')->nullable()->default(0);
            $table->unsignedInteger('rollup_resource_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->dropColumn('show_in_budget');
            $table->dropColumn('show_in_cost');
            $table->dropColumn('is_rolled_up');
            $table->dropColumn('rollup_resource_id');
        });
    }
}
