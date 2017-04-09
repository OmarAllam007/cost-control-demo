<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoqDisciplineToMasterShadows extends Migration
{
    public function up()
    {
        Schema::table('master_shadows', function (Blueprint $table) {
            $table->string('boq_discipline')->nullable();
        });
    }


    public function down()
    {
        Schema::table('master_shadows', function (Blueprint $table) {
            $table->dropColumn('boq_discipline');
        });
    }
}
