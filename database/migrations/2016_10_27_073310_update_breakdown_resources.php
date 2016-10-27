<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBreakdownResources extends Migration
{
    public function up()
    {
        Schema::table('breakdown_resources', function(Blueprint $table) {
            $table->integer('resource_id')->unsigned()->nullable();
            $table->string('equation')->nullable();
        });
    }

    public function down()
    {
        Schema::table('breakdown_resources', function(Blueprint $table) {
            $table->dropColumn('resource_id');
            $table->dropColumn('equation');
        });
    }
}
