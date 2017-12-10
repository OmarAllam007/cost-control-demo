<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunicationUsersTable extends Migration
{
    public function up()
    {
        Schema::create('communication_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('schedule_id');
            $table->unsignedInteger('user_id');
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('communication_users');
    }
}
