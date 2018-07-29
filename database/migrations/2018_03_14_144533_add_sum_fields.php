<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSumFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->boolean('is_sum')->default(0);
            $table->datetime('summed_at')->nullable();
            $table->unsignedInteger('sum_resource_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->dropColumn('is_sum');
            $table->dropColumn('summed_at');
        });
    }
}
