<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boqs', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('boq_divisions', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('breakdowns', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('business_partners', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('productivities', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('resource_types', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('std_activity_resources', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('survey_categories', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('units', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('wbs_levels', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        Schema::table('csi_categories', function (Blueprint $table) {
            $table->string('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boqs', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('boq_divisions', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('breakdowns', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('business_partners', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('productivities', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('resource_types', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('std_activity_resources', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('survey_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('wbs_levels', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('csi_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
}
