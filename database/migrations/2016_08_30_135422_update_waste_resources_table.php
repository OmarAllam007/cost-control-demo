<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWasteResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources', function (Blueprint $table) {
            $sql = 'UPDATE `resources` SET `waste` =  `waste` * 100 WHERE `waste` < 1';
            DB::connection()->update($sql);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resources', function (Blueprint $table) {
            $sql = 'UPDATE `resources` SET `waste` = CASE WHEN `waste` < 1 THEN `waste` / 100 WHEN `waste` > 1 THEN `waste` * 1 END';
            DB::connection()->update($sql);
        });
    }
}
