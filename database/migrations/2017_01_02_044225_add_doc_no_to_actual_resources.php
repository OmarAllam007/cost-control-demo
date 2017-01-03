<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocNoToActualResources extends Migration
{
    public function up()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->string('doc_no')->nullabel();
        });

        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->string('doc_no')->nullabel();
        });
    }

    public function down()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->dropColumn('doc_no');
        });

        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->dropColumn('doc_no');
        });
    }
}
