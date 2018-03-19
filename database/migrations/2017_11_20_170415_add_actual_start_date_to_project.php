<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActualStartDateToProject extends Migration
{
    function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->date('actual_start_date')->nullable();
        });
    }

    function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('actual_start_date');
        });
    }
}
