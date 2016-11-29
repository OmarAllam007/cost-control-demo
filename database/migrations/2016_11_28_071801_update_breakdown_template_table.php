<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBreakdownTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('breakdown_templates', function (Blueprint $table) {
            $table->integer('project_id')->nullable();
            $table->integer('wbs_id')->nullable();
            $table->integer('parent_template_id')->nullable();
        });
    }


    public function down()
    {
        Schema::table('breakdown_templates', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('wbs_id');
            $table->dropColumn('parent_template_id');
        });
    }
}
