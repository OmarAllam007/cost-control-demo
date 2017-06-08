<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisionBoqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_boqs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('revision_id');
            $table->unsignedInteger('boq_id')->nullable();

            $table->integer('wbs_id');
            $table->text('item');
            $table->text('description');
            $table->text('type')->nullable();
            $table->integer('unit_id');
            $table->integer('quantity');
            $table->double('dry_ur', 12, 2);
            $table->double('price_ur', 12, 2);
            $table->text('arabic_description')->nullable();
            $table->integer('division_id');
            $table->string('code', 255)->nullable();
            $table->string('item_code', 255);
            $table->string('cost_account', 255);
            $table->double('kcc_qty', 12, 2)->nullable();
            $table->double('subcon', 12, 2)->nullable();
            $table->double('materials', 12, 2)->nullable();
            $table->double('manpower', 12, 2)->nullable();
            $table->integer('project_id');

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
        Schema::drop('revision_boqs');
    }
}
