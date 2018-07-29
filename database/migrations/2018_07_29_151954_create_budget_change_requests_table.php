<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBudgetChangeRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('budget_change_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('wbs_id')->nullable();
            $table->unsignedInteger('activity_id')->nullable();
            $table->unsignedInteger('resource_id')->nullable();
            $table->text('description');
            $table->double('qty', 18, 6);
            $table->double('unit_price', 18, 6);
            $table->text('close_note')->nullable();
            $table->boolean('closed')->default(0);
            $table->dateTime('closed_at')->nullable();
            $table->unsignedInteger('closed_by')->nullable();
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('budget_change_requests');
    }
}
