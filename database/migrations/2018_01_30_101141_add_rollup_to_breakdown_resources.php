<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRollupToBreakdownResources extends Migration
{
    public function up()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->dateTime('rolled_up_at')->nullable();
            $table->boolean('is_rollup')->nullable()->default(0);
            $table->unsignedInteger('rollup_resource_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->dropColumn('rolled_up_at');
            $table->dropColumn('is_rollup');
            $table->dropColumn('rollup_resource_id');
        });
    }
}
