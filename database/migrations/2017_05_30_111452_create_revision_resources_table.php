<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisionResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('revision_id');
            $table->unsignedInteger('original_id')->nullable();

            $table->integer('resource_type_id');
            $table->string('resource_code', 255);
            $table->string('name', 255);
            $table->double('rate', 14, 2)->nullable();
            $table->string('unit', 255);
            $table->double('waste', 12, 2);
            $table->string('reference', 255);
            $table->unsignedInteger('business_partner_id');
            $table->unsignedInteger('project_id')->nullable();
            $table->unsignedInteger('resource_id')->nullable();
            $table->string('top_material', 255)->nullable();

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
        Schema::drop('revision_resources');
    }
}
