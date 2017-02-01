<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileToBatches extends Migration
{
    public function up()
    {
        Schema::table('actual_batches', function(Blueprint $table) {
            $table->string('file')->nullable();
        });
    }

    public function down()
    {
        Schema::table('actual_batches', function(Blueprint $table) {
            $table->removeColumn('file');
        });
    }
}
