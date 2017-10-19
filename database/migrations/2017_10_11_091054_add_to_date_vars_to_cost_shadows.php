<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToDateVarsToCostShadows extends Migration
{
    protected $connection = 'default';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->float('to_date_price_var', 16, 4)->nullable();
            $table->float('to_date_qty_var', 16, 4)->nullable();
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
            $table->dropColumn('to_date_price_var');
            $table->dropColumn('to_date_qty_var');
        });
    }
}
