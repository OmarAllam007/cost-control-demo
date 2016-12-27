<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResourceQtyManual extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->double('resource_qty',12,2)->nullable();
            $table->boolean('resource_qty_manual')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->dropColumn(['resource_qty_manual']);
        });
    }
}
