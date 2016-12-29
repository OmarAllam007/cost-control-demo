<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantitiesToStdActivityResource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('std_activity_resources', function (Blueprint $table) {
            $table->double('budget_qty',12,2)->nullable();
            $table->double('eng_qty',12,2)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('std_activity_resources', function (Blueprint $table) {
            $table->dropColumn('budget_qty');
            $table->dropColumn('eng_qty');
            $table->dropColumn('default_value');
        });
    }
}
