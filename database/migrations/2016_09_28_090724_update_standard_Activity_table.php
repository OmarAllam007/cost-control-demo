<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStandardActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('std_activities', function (Blueprint $table) {
            $table->dropColumn('breakdown_template_name');
            $table->string('work_package_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('std_activities', function (Blueprint $table) {
            $table->dropColumn('work_package_name');
        });
    }
}
