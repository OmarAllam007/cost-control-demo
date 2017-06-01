<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisionProductivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_productivities', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('revision_id');
            $table->unsignedInteger('original_id')->nullable();

            $table->unsignedInteger('project_id')->nullable();
            $table->string('csi_code', 255);
            $table->integer('csi_category_id');
            $table->string('description', 255);
            $table->string('unit', 255);
            $table->string('crew_structure', 255);
            $table->double('crew_hours', 12, 2);
            $table->double('crew_equip', 12, 2);
            $table->double('daily_output', 12, 2);
            $table->double('man_hours', 12, 2);
            $table->double('equip_hours', 12, 2);
            $table->double('reduction_factor', 12, 2);
            $table->double('after_reduction', 12, 2);
            $table->string('source', 255);
            $table->string('code', 255)->nullable();
            $table->unsignedInteger('productivity_id')->nullable();

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('revision_productivities');
    }
}
