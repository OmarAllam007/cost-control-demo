<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEacVarValuesToMasterShadows extends Migration
{
    public function up()
    {
        Schema::table('master_shadows', function (Blueprint $table) {
            $table->float('completion_var_optimistic', 12, 2)->nullable();
            $table->float('completion_var_likely', 12, 2)->nullable();
            $table->float('completion_var_pessimistic', 12, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('master_shadows', function (Blueprint $table) {
            $table->dropColumn('completion_var_optimistic');
            $table->dropColumn('completion_var_likely');
            $table->dropColumn('completion_var_pessimistic');
        });
    }
}
