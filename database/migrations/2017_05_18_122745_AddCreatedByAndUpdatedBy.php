<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByAndUpdatedBy extends Migration
{
    protected $tables = [
        'projects', 'wbs_levels', 'qty_surveys', 'boqs', 'periods', 'breakdowns',
        'breakdown_resources', 'breakdown_variables', 'breakdown_templates', 'productivities', 'project_users',
        'resource_codes', 'resource_types', 'resources', 'units', 'users', 'unit_aliases', 'std_activities',
        'std_activity_resources', 'std_activity_variables', 'cost_shadows', 'activity_maps', 'activity_divisions',
        'actual_resources', 'actual_batches', 'actual_revenue', 'break_down_resource_shadows'
    ];

    public function up()
    {
        /*foreach ($this->tables as $tableName) {
            Schema::table($tableName, function(Blueprint $table) {
                $table->unsignedInteger('created_by')->nullable();
                $table->unsignedInteger('updated_by')->nullable();
            });
        }*/
    }

    public function down()
    {
        /*foreach ($this->tables as $tableName) {
            Schema::table($tableName, function(Blueprint $table) {
                $table->dropColumn('created_by');
                $table->dropColumn('updated_by');
            });
        }*/
    }
}
