<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQsCodeToQtySurveys extends Migration
{

    public function up()
    {
        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->string('qs_code')->nullable();
        });
    }

    public function down()
    {
        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->dropColumn('qs_code');
        });
    }
}
