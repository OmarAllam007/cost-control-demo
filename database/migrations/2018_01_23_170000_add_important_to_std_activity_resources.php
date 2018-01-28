<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImportantToStdActivityResources extends Migration
{
    public function up()
    {
        Schema::table('std_activity_resources', function (Blueprint $table) {
            $table->boolean('important')->nullabel()->default(0);
        });
    }
    
    public function down()
    {
        $table->dropColumn('important');
    }
}