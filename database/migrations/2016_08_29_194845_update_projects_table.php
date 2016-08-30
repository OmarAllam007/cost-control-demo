<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('project_code')->nullable();
            $table->text('client_name')->nullable();
            $table->text('project_location')->nullable();
            $table->text('project_contract_value')->nullable();
            $table->date('project_start_date')->nullable();
            $table->text('project_duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('project_code')->nullable();
            $table->text('client_name')->nullable();
            $table->text('project_location')->nullable();
            $table->text('project_contract_value')->nullable();
            $table->date('project_start_date')->nullable();
            $table->text('project_duration')->nullable();
        });
    }
}
