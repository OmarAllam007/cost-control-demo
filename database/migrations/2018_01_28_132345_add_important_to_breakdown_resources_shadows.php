<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImportantToBreakdownResourcesShadows extends Migration
{
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->boolean('important')->nullable()->default(0);
        });
    }

    public function down()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table) {
            $table->dropColumn('important');
        });
    }
}
