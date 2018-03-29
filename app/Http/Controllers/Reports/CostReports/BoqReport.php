<?php
namespace App\Http\Controllers\Reports\CostReports;

use App\Boq;
use App\MasterShadow;
use App\Project;
use App\Period;
use App\WbsLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use PHPExcel_IOFactory;
use PHPExcel_Style_Color;
use PHPExcel_Style_Conditional;
use PHPExcel_Worksheet_MemoryDrawing;
use function storage_path;

class BoqReport
{
    /** @var Project */
    protected $project;

    /** @var Period */
    protected $period;

    /** @var Collection */
    protected $wbs_levels;

    protected $start = 9;

    protected $row = 8;

    function __construct(Period $period)
    {
        $this->period = $period;
        $this->project = $period->project;
        $this->wbs_levels = new Collection();
    }

    function run()
    {
        $project = $this->project;
        $period = $this->period;

        $tree = $this->buildTree();

        $periods = $this->project->periods()->readyForReporting()->orderBy('name')->pluck('name', 'id');

        return compact('tree', 'project', 'periods', 'period');
    }

    function buildTree()
    {
        $tree = [];

        $query = MasterShadow::with('boq_record', 'boq_wbs')->boqReport($this->period);
        $currentData = $this->applyFilters($query)->get();
        foreach ($currentData as $boq) {

            if ($this->wbs_levels->has($boq->boq_wbs_id)) {
                $levels = $this->wbs_levels->get($boq->boq_wbs_id);
            } else {
                if (!$boq->boq_wbs) {
                    continue;
                }
                $levels = $boq->boq_wbs->getParents();
                $this->wbs_levels->put($boq->boq_wbs_id, $levels);
            }

            $key = '';
            $lastKey = '';
            foreach ($levels as $level) {
                $lastKey = $key;
                $key .= $level;

                if (!isset($tree[$key])) {
                    $tree[$key] = [
                        'boqs' => [], 'budget_cost' => 0, 'to_date_cost' => 0, 'to_date_allowable' => 0, 'to_date_var' => 0,
                        'remaining_cost' => 0, 'at_completion_cost' => 0, 'at_completion_var' => 0, 'dry_cost' => 0, 'boq_cost' => 0
                    ];
                }

                $tree[$key]['name'] = $level;
                $tree[$key]['key'] = $key;
                $tree[$key]['parent'] = $lastKey;
                $tree[$key]['dry_cost'] += $boq->boq_record->dry_ur * $boq->boq_record->quantity;
                $tree[$key]['boq_cost'] += $boq->boq_record->price_ur * $boq->boq_record->quantity;
                $tree[$key]['budget_cost'] += $boq->budget_cost;
                $tree[$key]['to_date_cost'] += $boq->to_date_cost;
                $tree[$key]['to_date_allowable'] += $boq->to_date_allowable;
                $tree[$key]['to_date_var'] += $boq->to_date_var;
                $tree[$key]['remaining_cost'] += $boq->remaining_cost;
                $tree[$key]['at_completion_cost'] += $boq->at_completion_cost;
                $tree[$key]['at_completion_var'] += $boq->at_completion_var;

                $lastKey = $key;
            }

            $tree[$lastKey]['boqs'][] = [
                'cost_account' => $boq->cost_account,
                'description' => $boq->boq_record->description,
                'dry_price' => $boq->boq_record->dry_ur,
                'boq_price' => $boq->boq_record->price_ur,
                'budget_unit_rate' => $boq->budget_unit_rate,
                'dry_cost' => $boq->boq_record->dry_ur * $boq->boq_record->quantity,
                'boq_cost' => $boq->boq_record->price_ur * $boq->boq_record->quantity,
                'physical_qty' => $boq->physical_qty,
                'boq_qty' => $boq->boq_record->quantity,
                'budget_qty' => $boq->budget_qty,
                'budget_cost' => $boq->budget_cost,
                'to_date_cost' => $boq->to_date_cost,
                'to_date_allowable' => $boq->to_date_allowable,
                'to_date_var' => $boq->to_date_var,
                'remaining_cost' => $boq->remaining_cost,
                'at_completion_cost' => $boq->at_completion_cost,
                'at_completion_var' => $boq->at_completion_var,
            ];
        }

        return collect($tree);
    }

    function applyFilters(Builder $query)
    {
        $request = request();

        if ($status = strtolower($request->get('status', ''))) {
            if ($status == 'not started') {
                $query->havingRaw('sum(to_date_qty) = 0');
            } elseif ($status == 'in progress') {
                $query->havingRaw('sum(to_date_qty) > 0 AND AVG(progress) < 100');
            } elseif ($status == 'closed') {
                $query->where('to_date_qty', '>', 0)->where('progress', 100);
            }
        }

        if ($wbs = $request->get('wbs')) {
//            $term = "%$wbs%";

            $query->whereIn('wbs_id', $wbs);
        }

        if ($cost_account = $request->get('cost_account')) {
            $query->where('cost_account', 'like', "%$cost_account%");
        }

        if ($desc = $request->get('description')) {
            $query->where('boq', 'like', "%$desc%");
        }

        if ($request->exists('negative_to_date')) {
            $query->havingRaw('to_date_var < 0');
        }

        if ($request->exists('negative_completion')) {
            $query->having('at_completion_var', '<', 0);
        }

        return $query;
    }

    public function excel()
    {
        \Excel::create(slug($this->project->name) . 'cost_boq', function(LaravelExcelWriter $excel) {
            $excel->addExternalSheet($this->sheet());
            $excel->download('xlsx');
        });
    }

    public function sheet()
    {
        $data = $this->run();

        $tree = $data['tree'];
        $period = $data['period'];
        $project = $data['project'];

        $sheet = PHPExcel_IOFactory::load(storage_path('templates/cost-boq.xlsx'))->getSheet(0);

        $varCondition = new PHPExcel_Style_Conditional();
        $varCondition->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS);
        $varCondition->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_LESSTHAN);
        $varCondition->addCondition(0);
        $varCondition->getStyle()->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);

        $projectCell = $sheet->getCell('A4');
        $issueDateCell = $sheet->getCell('A5');
        $periodCell = $sheet->getCell('A6');

        $projectCell->setValue($projectCell->getValue() . ' ' . $project->name);
        $issueDateCell->setValue($issueDateCell->getValue() . ' ' . date('d M Y'));
        $periodCell->setValue($periodCell->getValue() . ' ' . $period->name);

        $logo = imagecreatefrompng(public_path('images/kcc.png'));
        $drawing = new PHPExcel_Worksheet_MemoryDrawing();
        $drawing->setName('Logo')->setImageResource($logo)
            ->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG)
            ->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG)
            ->setCoordinates('P2')->setWorksheet($sheet);

        $start = 10;
        $counter = $start;

        foreach($tree->where('parent', '')->sortBy('name') as $key => $level) {
            $counter = $this->renderWBS($sheet, $tree, $key, $level, $counter);
        }

        $sheet->getStyle("A{$start}:A{$counter}")->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle("B{$start}:Q{$counter}")->getNumberFormat()->setBuiltInFormatCode(40);
        $sheet->getStyle("N{$start}:N{$counter}")->setConditionalStyles([$varCondition]);
        $sheet->getStyle("N{$start}:Q{$counter}")->setConditionalStyles([$varCondition]);

        $sheet->setShowSummaryBelow(false);
        $sheet->setTitle('BOQ (Cost)');

        return $sheet;
    }

    protected function renderWBS($sheet, Collection $tree, $key, $level, $counter, $depth = 0)
    {
        $sheet->fromArray([
            str_repeat('    ', $depth) . $level['name'],
            '', '', '', '', '', '', '',
            $level['dry_cost'] ?: '0.00',
            $level['boq_cost'] ?: '0.00',
            $level['budget_cost'] ?: '0.00',
            $level['to_date_cost'] ?: '0.00',
            $level['to_date_allowable'] ?: '0.00',
            $level['to_date_var'] ?: '0.00',
            $level['remaining_cost'] ?: '0.00',
            $level['at_completion_cost'] ?: '0.00',
            $level['at_completion_var'] ?: '0.00',
        ], '', "A{$counter}");

        if ($depth > 0) {
            $sheet->getRowDimension($counter)->setOutlineLevel($depth)->setCollapsed(true)->setVisible(false);
        }
        ++$counter;

        $children = $tree->where('parent', $key)->sortBy('name') ;
        if ($children->count()) {
            foreach($children as $subkey => $child) {
                $counter = $this->renderWBS($sheet, $tree, $subkey, $child, $counter, $depth + 1);
            }
        }

        $depth += 1;

        if (!empty($level['boqs'])) {

            foreach($level['boqs'] as $boq) {
                $sheet->fromArray([
                    str_repeat('    ', $depth) . $boq['cost_account'],
                    $boq['description'],
                    $boq['dry_price'] ?: '0.00',
                    $boq['boq_price'] ?: '0.00',
                    $boq['budget_unit_rate'] ?: '0.00',
                    $boq['boq_qty'] ?: '0.00',
                    $boq['budget_qty'] ?: '0.00',
                    $boq['physical_qty'] ?: '0.00',
                    $boq['dry_cost'] ?: '0.00',
                    $boq['boq_cost'] ?: '0.00',
                    $boq['budget_cost'] ?: '0.00',
                    $boq['to_date_cost'] ?: '0.00',
                    $boq['to_date_allowable'] ?: '0.00',
                    $boq['to_date_var'] ?: '0.00',
                    $boq['remaining_cost'] ?: '0.00',
                    $boq['at_completion_cost'] ?: '0.00',
                    $boq['at_completion_var'] ?: '0.00',
                ], '', "A{$counter}");

                $sheet->getRowDimension($counter)->setOutlineLevel($depth)->setCollapsed(true)->setVisible(false);
                ++$counter;
            }
        }

        return $counter;
    }

}