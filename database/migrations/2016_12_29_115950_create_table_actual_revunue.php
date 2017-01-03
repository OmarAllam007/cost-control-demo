<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActualRevunue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actual_revenue', function (Blueprint $table) {
            $table->increments('id');
            $table->double('value',12,2);
            $table->string('cost_account');
            $table->integer('project_id');
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
        Schema::drop('actual_revenue');
    }
}
