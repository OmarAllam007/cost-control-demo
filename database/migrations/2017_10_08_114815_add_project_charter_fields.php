<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectCharterFields extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_type')->nullable();
            $table->string('contract_type')->nullable();
            $table->string('consultant')->nullable();
            $table->float('dry_cost', 18, 2)->nullable();
            $table->float('overhead_and_gr', 18, 2)->nullable();
            $table->float('estimated_profit_and_risk', 18, 2)->nullable();
            $table->text('description')->nullable()->change();
            $table->text('assumptions')->nullable();
            $table->text('discipline_brief')->nullable();
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('project_type');
            $table->dropColumn('contract_type');
            $table->dropColumn('consultant');
            $table->dropColumn('dry_cost');
            $table->dropColumn('overhead_and_gr');
            $table->dropColumn('estimated_profit_and_risk');
            $table->dropColumn('assumptions');
            $table->dropColumn('discipline_brief');
        });
    }
}
