<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginalDataToActualResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->text('original_data')->nullable();
        });

        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->text('original_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actual_resources', function (Blueprint $table) {
            $table->dropColumn('original_data');
        });

        Schema::table('cost_shadows', function (Blueprint $table) {
            $table->dropColumn('original_data');
        });
    }
}
