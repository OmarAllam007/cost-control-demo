<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakdownTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('breakdown_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('std_activity_id');
            $table->integer('project_id')->nullable();
//            $table->integer('wbs_id')->nullable();
            $table->integer('parent_template_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('breakdown_templates');
    }
}