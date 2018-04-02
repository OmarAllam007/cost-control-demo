<?php

namespace App\Reports\Cost;

use App\ActualRevenue;
use App\BreakDownResourceShadow;
use App\BudgetRevision;
use App\CostManDay;
use App\MasterShadow;
use App\Period;
use App\Project;
use App\Reports\Budget\ProfitabilityIndexReport;
use App\Revision\RevisionBreakdownResourceShadow;
use Carbon\Carbon;
use FontLib\TrueType\Collection;
use Illuminate\Support\Fluent;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Writer_Excel2007;
use Response;
use function slug;
use function storage_path;
use function str_random;

class ProjectInfo
{
    /** @var Period */
    private $period;

    /** @var Project */
    private $project;

    /** @var Collection */
    private $costSummary;

    public function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
    }

    function run()
    {
        $key = "project-info-{$this->project->id}-{$this->period->id}";
        if (request()->exists('clear') || request()->exists('refresh')) {
            \Cache::forget($key);
        }

        return \Cache::remember($key, Carbon::now()->addDay(), function () {
            return $this->getInfo();
        });
    }

    function getInfo()
    {
        $this->costSummary();

        $this->wasteIndexTrend = $this->project->periods()->latest('id')
            ->where('global_period_id', '>=', 12)
            ->readyForReporting()->take(6)->get()->reverse()->keyBy('id')->map(function ($period) {
                $report = new WasteIndexReport($period);
                $data = $report->run();

                $p = new Fluent(['name' => $period->name, 'value' => abs($data['total_pw_index'])]);
                return $p;
            });

        $this->wasteIndex = $this->wasteIndexTrend->get($this->period->id)->value;

        $this->productivityIndexTrend = $this->getProductivityIndexTrend();

        $this->cpiTrend = MasterShadow::where('master_shadows.project_id', $this->project->id)
            ->cpiTrendChart()->get()->reverse()->groupBy('p_id')->map(function ($period_items) {
                $items = $period_items->keyBy('resource_type_id');
                $allowable_cost = $items->sum('allowable_cost');
                $to_date_cost = $items->sum('to_date_cost');
                $reserve = $items->get(8, new Fluent());
                $budget_cost = $items->sum('budget_cost') - $reserve->budget_cost;
                $to_date_reserve = $reserve->budget_cost * $to_date_cost / $budget_cost;
                $allowable_cost += $to_date_reserve;

                $item = new Fluent();
                $item->p_name = $period_items->pluck('p_name')->first();
                $item->value = round($allowable_cost/$to_date_cost, 4);
                return $item;
            });

        $this->spiTrend = $this->project->periods()
            ->where('status', Period::GENERATED)
            ->where('global_period_id', '>=', 12)
            ->take(6)
            ->latest('id')->get(['name', 'spi_index'])->reverse();

        $cost = MasterShadow::where('period_id', $this->period->id)
            ->selectRaw('sum(to_date_cost) actual_cost, sum(remaining_cost) remaining_cost')->first();

        $completion_cost = $cost->actual_cost + $cost->remaining_cost;

        $this->actual_cost_percentage = round($cost->actual_cost * 100 / $completion_cost, 2);
        $this->remaining_cost_percentage = round($cost->remaining_cost * 100 / $completion_cost, 2);

        $this->actualRevenue = $this->getActualRevenue();

        return [
            'project' => $this->project,
            'costSummary' => $this->costSummary,
            'period' => $this->period,
            'periods' => Period::where(['project_id' => $this->project->id])->readyForReporting()->get(),
            'cpiTrend' => $this->cpiTrend,
            'spiTrend' => $this->spiTrend,
            'wasteIndex' => $this->wasteIndex,
            'wasteIndexTrend' => $this->wasteIndexTrend,
            'productivityIndexTrend' => $this->productivityIndexTrend,
            'actual_cost' => $cost->actual_cost,
            'actual_cost_percentage' => $this->actual_cost_percentage,
            'remaining_cost' => $cost->remaining_cost,
            'remaining_cost_percentage' => $this->remaining_cost_percentage,
            'actualRevenue' => $this->actualRevenue,
            'budgetInfo' => $this->getBudgetInfo(),
            'costInfo' => $this->getCostInfo()
        ];
    }

    function excel()
    {

        $excel = new PHPExcel();
        $excel->removeSheetByIndex(0);
        $excel->addExternalSheet($this->sheet());


        /** @var PHPExcel_Writer_Excel2007 $writer */
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->setOffice2003Compatibility(false)->setIncludeCharts(true);
        $writer->save($filename = storage_path('app/' . str_random() . '.xlsx'));

        return Response::download($filename, slug($this->project->name) . '-dashboard.xlsx')
            ->deleteFileAfterSend(true);
    }

    function sheet()
    {
        $data = $this->run();

        $template = storage_path('templates/dashboard.xlsx');
        /** @var PHPExcel_Reader_Excel2007 $reader */
        $reader = PHPExcel_IOFactory::createReaderForFile($template);
        $reader->setIncludeCharts(true);
        $file = $reader->load($template);
        $sheet = $file->getSheet(0);

        // Contract information
        $sheet->setCellValue('C10', $data['project']->project_contract_signed_value);
        $sheet->setCellValue('C12', $data['project']->tender_initial_profit);
        $sheet->setCellValue('H10', $data['project']->project_duration);
        $sheet->setCellValue('H12', $data['project']->tender_initial_profitability_index/100);
        $sheet->setCellValue('M10', $data['project']->project_start_date? \Carbon\Carbon::parse($data['project']->project_start_date)->format('d M Y') : '');
        $sheet->setCellValue('M12', $data['project']->expected_finish_date? \Carbon\Carbon::parse($data['project']->expected_finish_date)->format('d M Y') : '');

        // Revised Contract information
        $sheet->setCellValue('C16', $data['period']->change_order_amount);
        $sheet->setCellValue('C18', $data['period']->contract_value);
        $sheet->setCellValue('H16', $data['period']->time_extension);
        $sheet->setCellValue('H18', $data['period']->expected_duration);
        $sheet->setCellValue('M16', $data['project']->project_start_date? \Carbon\Carbon::parse($data['project']->project_start_date)->format('d M Y') : '');
        $sheet->setCellValue('M18', $data['period']->planned_finish_date->format('d M Y'));

        // Budget Data
        //Revision 0
        $sheet->setCellValue('E23', $data['budgetInfo']['revision0']['budget_cost']);
        $sheet->setCellValue('E24', $data['budgetInfo']['revision0']['direct_cost']);
        $sheet->setCellValue('E25', $data['budgetInfo']['revision0']['indirect_cost']);
        $sheet->setCellValue('E26', $data['budgetInfo']['revision0']['management_reserve']);
        $sheet->setCellValue('E27', $data['budgetInfo']['revision0']['eac_contract_amount']);
        $sheet->setCellValue('E28', $data['budgetInfo']['revision0']['profit']);
        $sheet->setCellValue('E29', $data['budgetInfo']['revision0']['profitability_index'] / 100);
        // Last Revision
        $sheet->setCellValue('J23', $data['budgetInfo']['revision1']['budget_cost']);
        $sheet->setCellValue('J24', $data['budgetInfo']['revision1']['direct_cost']);
        $sheet->setCellValue('J25', $data['budgetInfo']['revision1']['indirect_cost']);
        $sheet->setCellValue('J26', $data['budgetInfo']['revision1']['management_reserve']);
        $sheet->setCellValue('J27', $data['budgetInfo']['revision1']['eac_contract_amount']);
        $sheet->setCellValue('J28', $data['budgetInfo']['revision1']['profit']);
        $sheet->setCellValue('J29', $data['budgetInfo']['revision1']['profitability_index'] / 100);

        //Actual Data
        $sheet->setCellValue('C33', $data['costInfo']['actual_cost']);
        $sheet->setCellValue('C35', $data['costInfo']['allowable_cost']);
        $sheet->setCellValue('C37', $data['costInfo']['cpi']);
        $sheet->setCellValue('C39', $data['costInfo']['variance']);
        $sheet->setCellValue('C41', $data['costInfo']['cost_progress']/100);

        $sheet->setCellValue('H33', $data['period']->time_elapsed);
        $sheet->setCellValue('H35', $data['period']->time_remaining);
        $sheet->setCellValue('H37', $data['period']->actual_duration);
        $sheet->setCellValue('H39', $data['period']->duration_variance);
        $sheet->setCellValue('H41', $data['period']->actual_progress/100);

        $sheet->setCellValue('M33', $data['project']->actual_start_date? Carbon::parse($data['project']->actual_start_date)->format('d M Y') : '');
        $sheet->setCellValue('M35', $data['period']->forecast_finish_date? Carbon::parse($data['period']->forecast_finish_date)->format('d M Y') : '');
        $sheet->setCellValue('M37', $data['period']->spi_index);
        $sheet->setCellValue('M39', $data['costInfo']['waste_index']/100);
        $sheet->setCellValue('M41', $data['period']->eac_profitability_index / 100);

        // Cost Summary
        $sheet->setCellValue('C47', $data['costSummary']['DIRECT']->budget_cost);
        $sheet->setCellValue('C48', $data['costSummary']['INDIRECT']->budget_cost);
        $sheet->setCellValue('C49', $data['costSummary']['MANAGEMENT RESERVE']->budget_cost);
        $sheet->setCellValue('C50', $data['costSummary']->sum('budget_cost'));

        $sheet->setCellValue('D47', $data['costSummary']['DIRECT']->previous_cost);
        $sheet->setCellValue('D48', $data['costSummary']['INDIRECT']->previous_cost);
        $sheet->setCellValue('D49', $data['costSummary']['MANAGEMENT RESERVE']->previous_cost);
        $sheet->setCellValue('D50', $data['costSummary']->sum('previous_cost'));

        $sheet->setCellValue('E47', $data['costSummary']['DIRECT']->allowable_cost);
        $sheet->setCellValue('E48', $data['costSummary']['INDIRECT']->allowable_cost);
        $sheet->setCellValue('E49', $data['costSummary']['MANAGEMENT RESERVE']->allowable_cost);
        $sheet->setCellValue('E50', $data['costSummary']->sum('allowable_cost'));

        $sheet->setCellValue('F47', $data['costSummary']['DIRECT']->to_date_cost);
        $sheet->setCellValue('F48', $data['costSummary']['INDIRECT']->to_date_cost);
        $sheet->setCellValue('F49', $data['costSummary']['MANAGEMENT RESERVE']->to_date_cost);
        $sheet->setCellValue('F50', $data['costSummary']->sum('to_date_cost'));

        $sheet->setCellValue('G47', $data['costSummary']['DIRECT']->to_date_var);
        $sheet->setCellValue('G48', $data['costSummary']['INDIRECT']->to_date_var);
        $sheet->setCellValue('G49', $data['costSummary']['MANAGEMENT RESERVE']->to_date_var);
        $sheet->setCellValue('G50', $data['costSummary']->sum('to_date_var'));

        $sheet->setCellValue('H47', $data['costSummary']['DIRECT']->remaining_cost);
        $sheet->setCellValue('H48', $data['costSummary']['INDIRECT']->remaining_cost);
        $sheet->setCellValue('H49', $data['costSummary']['MANAGEMENT RESERVE']->remaining_cost);
        $sheet->setCellValue('H50', $data['costSummary']->sum('remaining_cost'));

        $sheet->setCellValue('I47', $data['costSummary']['DIRECT']->completion_cost_optimistic);
        $sheet->setCellValue('I48', $data['costSummary']['INDIRECT']->completion_cost_optimistic);
        $sheet->setCellValue('I49', $data['costSummary']['MANAGEMENT RESERVE']->completion_cost_optimistic);
        $sheet->setCellValue('I50', $data['costSummary']->sum('completion_cost_optimistic'));

        $sheet->setCellValue('J47', $data['costSummary']['DIRECT']->completion_cost_likely);
        $sheet->setCellValue('J48', $data['costSummary']['INDIRECT']->completion_cost_likely);
        $sheet->setCellValue('J49', $data['costSummary']['MANAGEMENT RESERVE']->completion_cost_likely);
        $sheet->setCellValue('J50', $data['costSummary']->sum('completion_cost_likely'));

        $sheet->setCellValue('K47', $data['costSummary']['DIRECT']->completion_cost_pessimistic);
        $sheet->setCellValue('K48', $data['costSummary']['INDIRECT']->completion_cost_pessimistic);
        $sheet->setCellValue('K49', $data['costSummary']['MANAGEMENT RESERVE']->completion_cost_pessimistic);
        $sheet->setCellValue('K50', $data['costSummary']->sum('completion_cost_pessimistic'));


        $sheet->setCellValue('L47', $data['costSummary']['DIRECT']->completion_var_optimistic);
        $sheet->setCellValue('L48', $data['costSummary']['INDIRECT']->completion_var_optimistic);
        $sheet->setCellValue('L49', $data['costSummary']['MANAGEMENT RESERVE']->completion_var_optimistic);
        $sheet->setCellValue('L50', $data['costSummary']->sum('completion_var_optimistic'));

        $sheet->setCellValue('M47', $data['costSummary']['DIRECT']->completion_var_likely);
        $sheet->setCellValue('M48', $data['costSummary']['INDIRECT']->completion_var_likely);
        $sheet->setCellValue('M49', $data['costSummary']['MANAGEMENT RESERVE']->completion_var_likely);
        $sheet->setCellValue('M50', $data['costSummary']->sum('completion_var_likely'));

        $sheet->setCellValue('N47', $data['costSummary']['DIRECT']->completion_var_pessimistic);
        $sheet->setCellValue('N48', $data['costSummary']['INDIRECT']->completion_var_pessimistic);
        $sheet->setCellValue('N49', $data['costSummary']['MANAGEMENT RESERVE']->completion_var_pessimistic);
        $sheet->setCellValue('N50', $data['costSummary']->sum('completion_var_pessimistic'));



        $sheet->setShowGridlines(false)->setPrintGridlines(false);
        return $sheet;
    }

    private function getProductivityIndexTrend()
    {
//        $periods = $this->project->periods()
//            ->where('status', Period::GENERATED)
//            ->where('global_period_id', '>=', 12)->take(6)->pluck('name', 'id');
//
//        $allowable_qty = MasterShadow::whereIn('period_id', $periods->keys())
//            ->where('resource_type_id', 2)
//            ->groupBy('period_id')->selectRaw('period_id, sum(allowable_qty) as allowable_qty')->get();
//
//        $cost_man_days = CostManDay::whereIn('period_id', $periods->keys()->toArray())
//            ->selectRaw('period_id, sum(actual) as actual')
//            ->groupBy('period_id')->get()->keyBy('period_id');
//
//        return $allowable_qty->map(function ($period) use ($cost_man_days, $periods) {
//            $actual = $cost_man_days->get($period->period_id)->actual ?? 0;
//            $value = 0;
//            if ($actual) {
//                $value = $period->allowable_qty / $actual;
//            }
//
//            $name = $periods->get($period->period_id, '');
//
//            return new Fluent(compact('name', 'value'));
//        });

        return $this->project->periods()->latest('global_period_id')->take(6)
            ->selectRaw('id, name, productivity_index as value')
//            ->whereNotNull('productivity_index')
            ->where('global_period_id', '>=', 12)
            ->where('status', Period::GENERATED)
            ->get()->reverse()->keyBy('id');
    }

    private function getActualRevenue()
    {
        $periods = $this->project->periods->pluck('name', 'id');
        return ActualRevenue::where('project_id', $this->project->id)
            ->selectRaw('period_id, sum(value) as value')
            ->groupBy('period_id')->get()->map(function ($period) use ($periods) {
                return new Fluent([
                    'name' => $periods->get($period->period_id, ''),
                    'value' => round($period->value, 2)
                ]);
            });
    }

    private function getBudgetInfo()
    {
        $first = BudgetRevision::where('project_id', $this->project->id)->orderBy('id')->first();
        $revision0 = [];

        if ($first) {
            $revision0['budget_cost'] = RevisionBreakdownResourceShadow::where('revision_id', $first->id)->sum('budget_cost');
            $revision0['revised_contract_amount'] = $first->revised_contract_amount;
            $revision0['general_requirements'] = RevisionBreakdownResourceShadow::where('revision_id', $first->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision0['management_reserve'] = RevisionBreakdownResourceShadow::where('revision_id', $first->id)
                ->where('resource_type_id', 8)->sum('budget_cost');
            $revision0['eac_contract_amount'] = $first->eac_contract_amount;
            $revision0['profit'] = $first->planned_profit_amount;
            $revision0['profitability_index'] = $first->planned_profitability_index;
        } else {
            $revision0['budget_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');
            $revision0['revised_contract_amount'] = $this->project->revised_contract_amount;
            $revision0['general_requirements'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision0['management_reserve'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 8)->sum('budget_cost');
            $revision0['eac_contract_amount'] = $this->project->eac_contract_amount;
            $revision0['profit'] = $this->project->planned_profit_amount;
            $revision1['profitability_index'] = $this->project->planned_profitability;
        }

        $revision0['indirect_cost'] = $revision0['general_requirements'];
        $revision0['direct_cost'] = $revision0['budget_cost'] - $revision0['indirect_cost'] - $revision0['management_reserve'];

        $latest = BudgetRevision::where('project_id', $this->project->id)->where('is_generated', 1)->latest()->first();

        $revision1 = [];

        if ($latest) {
            $revision1['budget_cost'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)->sum('budget_cost');
            $revision1['revised_contract_amount'] = $latest->revised_contract_amount;
            $revision1['general_requirements'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision1['management_reserve'] = RevisionBreakdownResourceShadow::where('revision_id', $latest->id)
                ->where('resource_type_id', 8)->sum('budget_cost');
            $revision1['eac_contract_amount'] = $latest->eac_contract_amount;
            $revision1['profit'] = $latest->planned_profit_amount;
            $revision1['profitability_index'] = $latest->planned_profitability_index;
        } else {
            $revision1['budget_cost'] = BreakDownResourceShadow::where('project_id', $this->project->id)->sum('budget_cost');
            $revision1['revised_contract_amount'] = $this->project->revised_contract_amount;
            $revision1['general_requirements'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 1)->sum('budget_cost');
            $revision1['management_reserve'] = BreakDownResourceShadow::where('project_id', $this->project->id)
                ->where('resource_type_id', 8)->sum('budget_cost');
            $revision1['eac_contract_amount'] = $this->project->eac_contract_amount;
            $revision1['profit'] = $this->project->planned_profit_amount;
            $revision1['profitability_index'] = $this->project->planned_profitability;
        }

        $revision1['indirect_cost'] = $revision1['general_requirements'];
        $revision1['direct_cost'] = $revision1['budget_cost'] - $revision1['indirect_cost'] - $revision1['management_reserve'];

        return compact('revision0', 'revision1');
    }

    private function getCostInfo()
    {
        $info = [
            'spi' => $this->period->spi_index,
            'actual_cost' => $this->costSummary->sum('to_date_cost'),
            'allowable_cost' => $this->costSummary->sum('allowable_cost'),
        ];

        $budget_cost = MasterShadow::where('period_id', $this->period->id)->sum('budget_cost');

        $info['variance'] = $info['allowable_cost'] - $info['actual_cost'];
        $info['cpi'] = $info['allowable_cost'] / $info['actual_cost'];
        $info['cost_progress'] = $info['actual_cost'] * 100 / $budget_cost;
        $info['waste_index'] = $this->wasteIndex ?? 0;
        $info['time_progress'] = $this->period->actual_progress;
        $info['productivity_index'] = $this->productivityIndexTrend->where('period_id', $this->period->id)->value ?? 0;
        $info['actual_start_date'] = $this->project->actual_start_date;

        return $info;
    }

    private function costSummary()
    {
        $this->costSummary = MasterShadow::dashboardSummary($this->period)->get()->keyBy('type');

        if ($this->costSummary->has('MANAGEMENT RESERVE')) {
            $reserve = $this->costSummary->get('MANAGEMENT RESERVE');
            $reserve->completion_cost = $reserve->remaining_cost = 0;
            $reserve->completion_cost_likely = $reserve->completion_cost_optimistic = $reserve->completion_cost_pessimestic = 0;
            $reserve->completion_var_likely = $reserve->budget_cost;
            $reserve->completion_var_optimistic = $reserve->budget_cost;
            $reserve->completion_var_pessimistic = $reserve->budget_cost;

            $progress = min(1, $this->costSummary->sum('to_date_cost') / $this->costSummary->sum('budget_cost'));
            $reserve->to_date_var = $reserve->allowable_cost = $progress * $reserve->budget_cost;
        }

        if ($this->costSummary->has('INDIRECT')) {
            $indirect = $this->costSummary->get('INDIRECT');

            $indirect->completion_cost_likely = $this->period->at_completion_likely;
            $indirect->completion_var_likely = $indirect->budget_cost - $this->period->at_completion_likely;

            $indirect->completion_cost_optimistic = $this->period->at_completion_optimistic;
            $indirect->completion_var_optimistic = $indirect->budget_cost - $this->period->at_completion_optimistic;

            $indirect->completion_cost_pessimistic = $this->period->at_completion_pessimistic;
            $indirect->completion_var_pessimistic = $indirect->budget_cost - $this->period->at_completion_pessimistic;
        }

        if ($this->costSummary->has('DIRECT')) {
            $direct = $this->costSummary->get('DIRECT');
            $direct->completion_cost_likely = $direct->completion_cost;
            $direct->completion_var_likely = $direct->completion_var;
            $direct->completion_cost_optimistic = $direct->completion_cost;
            $direct->completion_var_optimistic = $direct->completion_var;
            $direct->completion_cost_pessimistic = $direct->completion_cost;
            $direct->completion_var_pessimistic = $direct->completion_var;
        }

//        dd($this->costSummary);
    }
}