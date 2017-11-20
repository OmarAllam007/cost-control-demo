<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpiToPeriods extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->float('spi_index')->default(0)->nullable();
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn('spi_index');
        });
    }
}
