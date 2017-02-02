<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCostIssuesTable extends Migration
{

    public function up()
    {
        Schema::create('cost_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('batch_id');
            $table->string('type');
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('cost_issues');
    }
}
