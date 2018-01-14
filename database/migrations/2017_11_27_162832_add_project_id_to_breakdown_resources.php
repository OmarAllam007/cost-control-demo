<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectIdToBreakdownResources extends Migration
{

    public function up()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('wbs_id');
        });

        DB::update('UPDATE breakdown_resources br join breakdowns b on (br.breakdown_id = b.id) set br.project_id = b.project_id, br.wbs_id = b.id');
    }

    public function down()
    {
        Schema::table('breakdown_resources', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('wbs_id');
        });
    }
}
