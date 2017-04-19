<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoqToBreakdownShadows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->unsignedInteger('boq_id');
            $table->unsignedInteger('survey_id');
            $table->unsignedInteger('boq_wbs_id');
            $table->unsignedInteger('survey_wbs_id');

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
            $table->dropColumn('boq_id');
            $table->dropColumn('boq_wbs_id');
            $table->dropColumn('survey_id');
            $table->dropColumn('survey_wbs_id');
        });
    }
}
