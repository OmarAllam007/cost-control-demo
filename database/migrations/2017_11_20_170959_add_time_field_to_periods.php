<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeFieldToPeriods extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->integer('time_elapsed')->nullable();
            $table->integer('time_remaining')->nullable();
            $table->integer('expected_duration')->nullable();
            $table->integer('duration_variance')->nullable();
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('time_elapsed');
            $table->dropColumn('time_remaining');
            $table->dropColumn('expected_duration');
            $table->dropColumn('duration_variance');
        });
    }
}
