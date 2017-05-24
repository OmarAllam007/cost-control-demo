<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManualEditToCostShadow extends Migration
{
    public function up()
    {
        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->boolean('manual_edit')->default(0)->nullable();
        });
    }

    public function down()
    {
        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->dropColumn('manual_edit');
        });
    }
}
