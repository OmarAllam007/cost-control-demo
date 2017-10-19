<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBudgetRevisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('budget_revisions', function (Blueprint $table) {
            $table->float('original_contract_amount', 18,2);
            $table->float('change_order_amount', 18,2);
        });

        \DB::statement('ALTER TABLE budget_revisions ADD COLUMN revised_contract_amount float(18,2) AS (original_contract_amount + change_order_amount)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('budget_revisions', function (Blueprint $table) {
            $table->float('original_contract_amount', 18,2);
            $table->float('change_order_amount', 18,2);
            $table->float('revised_contract_amount', 18,2);
        });
    }
}
