<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->integer('wbs_level_id');
            $table->integer('project_id');
            $table->string('cost_account');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->dropColumn('wbs_level_id');
            $table->dropColumn('project_id');
            $table->dropColumn('cost_account');
        });
    }
}
