<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToPeriods extends Migration
{
    public function up()
    {
        /*Schema::table('periods', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->nunllable();
        });*/
    }

    public function down()
    {
        /*Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('status');
        });*/
    }
}
