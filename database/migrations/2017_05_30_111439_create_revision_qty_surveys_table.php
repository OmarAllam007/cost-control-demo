<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisionQtySurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_qty_surveys', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('revision_id');
            $table->unsignedInteger('qty_survey_id')->nullable();

            $table->string('cost_account', 255);
            $table->string('description', 255);
            $table->unsignedInteger('unit_id');
            $table->double('budget_qty', 12, 2);
            $table->double('eng_qty', 12, 2);
            $table->softDeletes();
            $table->integer('wbs_level_id');
            $table->integer('project_id');
            $table->string('code', 255)->nullable();
            $table->string('discipline', 255)->nullable();

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
        Schema::drop('revision_qty_surveys');
    }
}
