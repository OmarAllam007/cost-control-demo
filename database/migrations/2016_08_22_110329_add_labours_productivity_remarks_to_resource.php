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
            $table->double('resource_waste',12,2)->nullable();
            $table->double('labor_count',12,2)->nullable();
            $table->integer('productivity_id')->unsigned()->nullable();
            $table->string('remarks')->nullable();
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
            $table->dropColumn('resource_waste');
            $table->dropColumn('labor_count');
            $table->dropColumn('productivity_id');
            $table->dropColumn('remarks');
        });
    }
}
