<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLaboursProductivityRemarksToResource extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('std_activity_resources', function (Blueprint $table) {
            $table->float('labors_count')->nullable();
            $table->integer('productivity_id')->unsigned()->nullable();
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('std_activity_resources', function (Blueprint $table) {
            $table->dropColumn('labors_count');
            $table->dropColumn('productivity_id');
            $table->dropColumn('remarks');
        });
    }
}
