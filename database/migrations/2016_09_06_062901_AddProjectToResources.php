<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectToResources extends Migration
{
    public function up()
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->integer('project_id', false, true)->nullable();
            $table->integer('resource_id', false, true)->nullable();
        });
    }

    public function down()
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('resource_id');
        });
    }
}
