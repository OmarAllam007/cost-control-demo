<?php

use App\Report;
use App\Reports\Budget\ActivityResourceBreakDownReport;
use App\Reports\Budget\BoqPriceListReport;
use App\Reports\Budget\BudgetCostByDisciplineReport;
use App\Reports\Budget\BudgetCostByResourceTypeReport;
use App\Reports\Budget\BudgetCostDryCostByBuildingReport;
use App\Reports\Budget\BudgetCostDryCostByDisciplineReport;
use App\Reports\Budget\BudgetTrendReport;
use App\Reports\Budget\CharterReport;
use App\Reports\Budget\ComparisonReport;
use App\Reports\Budget\HighPriorityMaterialsReport;
use App\Reports\Budget\ManPowerReport;
use App\Reports\Budget\ProductivityReport;
use App\Reports\Budget\ProfitabilityIndexReport;
use App\Reports\Budget\QsSummaryReport;
use App\Reports\Budget\ResourceDictReport;
use App\Reports\Budget\RevisedBoqReport;
use App\Reports\Budget\StdActivityReport;
use App\Reports\Budget\WbsDictionary;
use App\Reports\Budget\WbsLabours;
use App\Reports\Budget\WbsReport;
use App\Reports\Cost\ActivityReport;
use App\Reports\Cost\CostStandardActivityReport;
use App\Reports\Cost\CostSummary;
use App\Reports\Cost\ProductivityIndexReport;
use App\Reports\Cost\ThresholdReport;
use App\Reports\Cost\WasteIndexReport;
use Illuminate\Database\Seeder;

class ReportsSeeder extends Seeder
{
    public function run()
    {
        Report::unguard();

        $now = date('Y-m-d H:i:s');

        \DB::table('reports')->truncate();
        //Insert Budget Reports
        \DB::table('reports')->insert([
            ['name' => 'WBS (Control Point)', 'description' => '', 'class_name' => WbsReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Standard Activity Cost', 'description' => '', 'class_name' => StdActivityReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'WBS Dictionary', 'description' => '', 'class_name' => WbsDictionary::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Man Day By Control Point', 'description' => '', 'class_name' => WbsLabours::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Labour Budget (Cost-Unit)', 'description' => '', 'class_name' => ManPowerReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Revised BOQ (EAC Contract)', 'description' => '', 'class_name' => RevisedBoqReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Project Charter', 'description' => '', 'class_name' => CharterReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Profitability Report', 'description' => '', 'class_name' => ProfitabilityIndexReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Productivity', 'description' => '', 'class_name' => ProductivityReport::class, 'type' => 'Budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Activity Resource Breakdown', 'class_name' => ActivityResourceBreakDownReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'BOQ Price List', 'class_name' => BoqPriceListReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Budget Cost By Discipline', 'class_name' => BudgetCostByDisciplineReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'BUDGET COST BY ITEM BREAKDOWN', 'class_name' => BudgetCostByResourceTypeReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'BUDGET COST V.S DRY COST BY BUILDING', 'class_name' => BudgetCostDryCostByBuildingReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'BUDGET COST V.S DRY COST BY DISCIPLINE', 'class_name' => BudgetCostDryCostByDisciplineReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Budget Trend', 'class_name' => BudgetTrendReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Comparison Report', 'class_name' => ComparisonReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'High Priority Material', 'class_name' => HighPriorityMaterialsReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'QS Summary', 'class_name' => QsSummaryReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'BUDGET COST V.S DRY COST QTY & COST', 'class_name' => QtyAndCostReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Resource Dictionary', 'class_name' => ResourceDictReport::class, ' type' => 'budget', 'created_at' => $now, 'updated_at' => $now],
        ]);

        //Insert Cost Reports
        \DB::table('reports')->insert([
            ['name' => 'Cost Summary', 'description' => '', 'class_name' => CostSummary::class, 'type' => 'Cost Control'],
            ['name' => 'Activity', 'description' => '', 'class_name' => ActivityReport::class, 'type' => 'Cost Control'],
            ['name' =>'Standard Activity', 'description' => '', 'class_name' => CostStandardActivityReport::class, 'type' => 'Cost Control'],
            ['name' =>'Profitability Report', 'description' => '', 'class_name' => ProfitabilityIndexReport::class, 'type' => 'Cost Control'],
            ['name' =>'Productivity Report', 'description' => '', 'class_name' => ProductivityIndexReport::class, 'type' => 'Cost Control'],
            ['name' =>'Threshold Report', 'description' => '', 'class_name' => ThresholdReport::class, 'type' => 'Cost Control'],
            ['name' =>'Material Consumption Report', 'description' => '', 'class_name' => WasteIndexReport::class, 'type' => 'Cost Control'],
        ]);
    }
}
