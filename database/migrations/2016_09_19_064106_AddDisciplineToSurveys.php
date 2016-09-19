<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDisciplineToSurveys extends Migration
{
    public function up()
    {
        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->dropColumn('category_id');
            $table->string('discipline')->nullable();
        });

        Schema::table('survey_categories', function(Blueprint $table) {
            $table->dropIfExists();
        });
    }

    public function down()
    {
        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->integer('category_id')->nullable();
            $table->dropColumn('discipline');
        });
    }
}
