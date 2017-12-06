<?php


use App\Http\Controllers\Reports\CostReports\ActivityReport;
use App\Http\Controllers\Reports\CostReports\CostStandardActivityReport;
use App\Http\Controllers\Reports\CostReports\CostSummary;
use App\Report;
use App\Reports\Budget\ProfitabilityIndexReport;
use App\Reports\Budget\StdActivityReport;
use App\Reports\Budget\WbsReport;
use Illuminate\Database\Seeder;

class ReportsSeeder extends Seeder
{
    public function run()
    {
        Report::unguard();

        $now = date('Y-m-d H:i:s');

        \DB::table('reports')->truncate();

        //Insert Cost Reports
        \DB::table('reports')->insert([
            ['name' => '', 'description' => '', 'class_name' => CostSummary::class, 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => ActivityReport::class, 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => CostStandardActivityReport::class, 'type' => 'Cost Control'],
            ['name' => '', 'description' => '', 'class_name' => ProfitabilityIndexReport::class, 'type' => 'Cost Control'],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\CostControl\\', 'type' => 'Cost Control'],
        ]);

        //Insert Budget Reports
        \DB::table('reports')->insert([
            ['name' => '', 'description' => '', 'class_name' => WbsReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => StdActivityReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => App\Reports\Budget\WbsDictionary::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => App\Reports\Budget\WbsLabours::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => '', 'description' => '', 'class_name' => App\Reports\Budget\ManPowerReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
//            ['name' => '', 'description' => '', 'class_name' => 'App\\Reports\\Budget\\', 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
