<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBreakdownResourcesShadowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->integer('productivity_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->integer('template_id')->unsigned();
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
            $table->dropColumn('productivity_id');
            $table->dropColumn('unit_id');
            $table->dropColumn('template_id');
        });
    }
}
