<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBudgetUnitRateToCostShadow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->float('budget_unit_rate', 16, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->dropColumn();
        });
    }
}
