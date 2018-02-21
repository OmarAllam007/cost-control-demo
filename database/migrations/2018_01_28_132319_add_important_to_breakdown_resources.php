<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImportantToBreakdownResources extends Migration
{
    public function up()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->boolean('important')->nullable()->default(0);
        });
    }

    public function down()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
//            $table->dropColumn('important');
        });
    }
}