<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCostAuthorityToProjectsUsers extends Migration
{
    public function up()
    {
        Schema::table('project_users', function (Blueprint $table) {
            $table->boolean('activity_mapping')->nullable();
            $table->boolean('resource_mapping')->nullable();
            $table->boolean('periods')->nullable();
            $table->boolean('remaining_unit_price')->nullable();
            $table->boolean('remaining_unit_qty')->nullable();
            $table->boolean('manual_edit')->nullable();
            $table->boolean('delete_resources')->nullable();
        });
    }

    public function down()
    {
        Schema::table('project_users', function (Blueprint $table) {
            $table->dropColumn('activity_mapping');
            $table->dropColumn('resource_mapping');
            $table->dropColumn('periods');
            $table->dropColumn('remaining_unit_price');
            $table->dropColumn('remaining_unit_qty');
            $table->dropColumn('manual_edit');
            $table->dropColumn('delete_resources');
        });
    }
}
