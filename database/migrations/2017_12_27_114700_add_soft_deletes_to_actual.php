<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesToActual extends Migration
{
    public function up()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
