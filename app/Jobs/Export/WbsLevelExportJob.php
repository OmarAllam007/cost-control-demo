<?php

namespace App\Jobs\Export;

use App\Jobs\Job;
use App\Support\WBSTree;
use function array_reverse;
use Illuminate\Support\Collection;
use PHPExcel_IOFactory;
use PHPExcel_Style_Color;
use PHPExcel_Worksheet;
use SplStack;
use function storage_path;

class WbsLevelExportJob extends Job
{
    public $project;

    /** @var Collection */
    protected $tree;

    protected $row = 1;

    /** @var SplStack */
    protected $stack;

    public function __construct($project)
    {
        $this->project = $project;
    }

    /**
     * @return string
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function handle()
    {
        $excel = new \PHPExcel();
        $sheet = $excel->getActiveSheet();
        $sheet->fromArray([
            'Code', 'SAP Code', 'WBS Level 1', 'WBS Level 2', 'Wbs Level 3',
            'Wbs Level 4', 'Wbs Level 5', 'Wbs Level 6', 'Wbs Level 7',
            'Wbs Level 8', 'Wbs Level 9', 'Wbs Level 10',]);

        $this->tree = (new WBSTree($this->project))->get();

        $this->stack = new SplStack();

        foreach ($this->tree as $level) {
            $this->addLevel($sheet, $level);
        }

        $sheet->setAutoFilter("A1:L{$this->row}");
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle("{$col}1:{$col}{$this->row}");
        }
        $sheet->getStyle("A1:L1")->getFill()
            ->setFillType('solid')
            ->setStartColor(new PHPExcel_Style_Color('3490DC'));
        $sheet->getStyle("A1:L1")->getFont()
            ->setBold(true)
            ->setColor(new PHPExcel_Style_Color('EFF8FF'));

        $filename = storage_path('app/wbs_level_' . $this->project->id . '.xlsx');
        PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save($filename);
        return $filename;
    }

    /**
     * @param $sheet PHPExcel_Worksheet
     * @param $level
     * @param int $depth
     * @throws \PHPExcel_Exception
     */
    private function addLevel($sheet, $level, $depth = 0)
    {
        ++$this->row;

        while($depth < $this->stack->count()) {
            $this->stack->pop();
        }

        $this->stack->push($level->name);

        $row = [];
        foreach ($this->stack as $name) {
            $row[] = $name;
        }

        $row[] = $level->sap_code;
        $row[] = $level->code;

        $sheet->fromArray(array_reverse($row), null, "A{$this->row}", true);

        foreach ($level->subtree as $sublevel) {
            $this->addLevel($sheet, $sublevel, $depth + 1);
        }
    }
}
