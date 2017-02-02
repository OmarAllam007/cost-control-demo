<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->date('original_finished_date');
            $table->date('expected_finished_date');
            $table->text('project_contract_signed_value',15,2)->nullable();
            $table->text('project_contract_budget_value',15,2)->nullable();
            $table->text('change_order_amount',15,2)->nullable();
            $table->text('direct_cost_material',15,2)->nullable();
            $table->text('indirect_cost_general',15,2)->nullable();
            $table->text('total_budget_cost',15,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('original_finished_date');
            $table->dropColumn('expected_finished_date');
            $table->dropColumn('project_contract_signed_value');
            $table->dropColumn('project_contract_budget_value');
            $table->dropColumn('change_order_amount');
            $table->dropColumn('direct_cost_material');
            $table->dropColumn('indirect_cost_general');
            $table->dropColumn('total_budget_cost');
        });
    }
}
