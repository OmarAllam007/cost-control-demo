<?php

namespace App\Jobs\PrintReport;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\Reports\ActivityResourceBreakDown;
use App\Http\Controllers\Reports\BoqPriceList;
use App\Http\Controllers\Reports\BudgetCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostByDiscipline;
use App\Http\Controllers\Reports\BudgetCostDryCostByBuilding;
use App\Http\Controllers\Reports\BudgetCostDryCostByDiscipline;
use App\Http\Controllers\Reports\HighPriorityMaterials;
use App\Http\Controllers\Reports\Productivity;
use App\Http\Controllers\Reports\QtyAndCost;
use App\Http\Controllers\Reports\QuantitiySurveySummery;
use App\Http\Controllers\Reports\ResourceDictionary;
use App\Http\Controllers\Reports\RevisedBoq;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrintAllJob extends Job
{
    public $project;
    public function __construct($project)
    {
        $this->project = $project;
    }


    public function handle()
    {
        ini_set('max_execution_time',100);
        //Budget Calculations
        $wbsLevelReport = new ReportController();
        $wbsLevelReportHtml = $wbsLevelReport->wbsReport($this->project)->render();


        $stdActivityReport = new ReportController();
        $stdActivityReportHtml = $stdActivityReport->stdActivityReport($this->project)->render();


        $productivity = new Productivity();
        $productivityHtml = $productivity->getProductivity($this->project)->render();

        $qsSummery = new QuantitiySurveySummery();
        $qsSummeryHtml = $qsSummery->qsSummeryReport($this->project)->render();

        $boqPriceList = new BoqPriceList();
        $boqPriceListHtml = $boqPriceList->getBoqPriceList($this->project)->render();
        //Budget Output
        $resourceDictionary = new ResourceDictionary();
        $resourceDictionaryHtml = $resourceDictionary->getResourceDictionary($this->project)->render();

        $highPriorityMaterials = new HighPriorityMaterials();
        $highPriorityMaterialsHtml = $highPriorityMaterials->getTopHighPriorityMaterials($this->project)->render();

        $budgetManPower = new ReportController();
        $budgetManPowerHtml = $budgetManPower->manPower($this->project)->render();

        $budgetSummery = new ReportController();
        $budgetSummeryHtml = $budgetSummery->budgetSummery($this->project)->render();

        $activityResourceBreakDown = new ActivityResourceBreakDown();
        $activityResourceBreakDownHtml = $activityResourceBreakDown->getActivityResourceBreakDown($this->project)->render();

        $revisedBoq = new RevisedBoq();
        $revisedBoqHtml = $revisedBoq->getRevised($this->project)->render();

        //Budget Reports
        $budgetCostByBuilding = new BudgetCostByBuilding();
        $budgetCostByBuildingHtml = $budgetCostByBuilding->getBudgetCostForBuilding($this->project)->render();

        $budgetCostByDiscipline = new BudgetCostByDiscipline();
        $budgetCostByDisciplineHtml = $budgetCostByDiscipline->compareBudgetCostDiscipline($this->project)->render();

        $budgetCostByBreakDown = new ReportController();
        $budgetCostByBreakDownHtml = $budgetCostByBreakDown->budgetCostVSBreadDown($this->project)->render();

        $budgetCostDryCostByBuilding = new BudgetCostDryCostByBuilding();
        $budgetCostDryCostByBuildingHtml = $budgetCostDryCostByBuilding->compareBudgetCostDryCost($this->project)->render();

        $budgetCostDryCostByDiscipline = new BudgetCostDryCostByDiscipline();
        $budgetCostDryCostByDisciplineHtml = $budgetCostDryCostByDiscipline->compareBudgetCostDryCostDiscipline($this->project)->render();

        $budgetCostVsDry = new QtyAndCost();
        $budgetCostVsDryHtml = $budgetCostVsDry->compare($this->project)->render();



        echo $wbsLevelReportHtml .
            $stdActivityReportHtml .
            $productivityHtml .
            $qsSummeryHtml .
            $boqPriceListHtml .
            $resourceDictionaryHtml .
            $highPriorityMaterialsHtml .
            $budgetManPowerHtml .
            $budgetSummeryHtml .
            $activityResourceBreakDownHtml .
            $revisedBoqHtml .
            $budgetCostByBuildingHtml .
            $budgetCostByDisciplineHtml .
            $budgetCostByBreakDownHtml .
            $budgetCostDryCostByBuildingHtml .
            $budgetCostDryCostByDisciplineHtml .
            $budgetCostVsDryHtml;
    }
}
