<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoqQsIdToRevisionShadows extends Migration
{
    public function up()
    {
        Schema::table('revision_breakdown_resource_shadows', function (Blueprint $table) {
            $table->unsignedInteger('boq_qs_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('revision_breakdown_resource_shadows', function (Blueprint $table) {
            $table->dropColumn('boq_qs_id');
        });
    }
}
