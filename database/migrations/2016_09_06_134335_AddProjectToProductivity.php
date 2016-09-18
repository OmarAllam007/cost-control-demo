<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectToProductivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productivities', function (Blueprint $table) {
            $table->integer('project_id')->nullable()->unsigned();
            $table->integer('productivity_id')->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productivities', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('productivity_id');
        });
    }
}
