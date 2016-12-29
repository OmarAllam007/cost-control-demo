<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgressToBudgetShadow extends Migration
{
    public function up()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table){
            $table->double('progress',12,2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('break_down_resource_shadows', function (Blueprint $table){
            $table->dropColumn('progress');
        });
    }
}
