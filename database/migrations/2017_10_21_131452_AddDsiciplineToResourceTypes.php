<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDsiciplineToResourceTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resource_types', function (Blueprint $table) {
            $table->boolean('archived')->nullable()->default(0);
            $table->string('discipline')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resource_types', function (Blueprint $table) {
            $table->boolean('archived');
            $table->dropColumn('discipline');
        });
    }
}