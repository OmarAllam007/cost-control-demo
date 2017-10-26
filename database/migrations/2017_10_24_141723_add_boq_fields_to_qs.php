<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoqFieldsToQs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->unsignedInteger('boq_id')->nullable();
            $table->string('item_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qty_surveys', function (Blueprint $table) {
            $table->dropColumn('boq_id');
            $table->dropColumn('item_coed');
        });
    }
}
