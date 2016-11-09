<?php

namespace App\Http\Controllers;

use App\Boq;
use App\BoqDivision;
use App\Http\Requests\WipeRequest;
use App\Jobs\BoqImportJob;
use App\Project;
use App\Unit;
use App\UnitAlias;
use App\WbsLevel;
use Illuminate\Http\Request;

class BoqController extends Controller
{

    protected $rules = ['project_id' => 'required', 'wbs_id' => 'required', 'cost_account' => 'required'];

    public function index()
    {
        $boqs = Boq::paginate();

        return view('boq.index', compact('boqs'));
    }

    public function create(Request $request)

    {
        if (!$request->has('project')) {
            flash('Project not found');
            return redirect()->route('project.index');
        } else {
            $project = Project::find($request->get('project'));
            if (!$project) {
                flash('Project not found');
                return redirect()->route('project.index');
            }
        }
        $wbsLevels = WbsLevel::tree()->paginate();

        return view('boq.create', compact('wbsLevels'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $boq = Boq::create($request->all());

        flash('Boq has been saved', 'success');
        return \Redirect::route('project.show', $boq->project_id);
    }

    public function show(Boq $boq)
    {
        return view('boq.show', compact('boq'));
    }

    public function edit(Boq $boq)
    {
        return view('boq.edit', compact('boq'));
    }

    public function update(Boq $boq, Request $request)
    {
        $this->validate($request, $this->rules);
        $boq->update($request->all());

        flash('Boq has been saved', 'success');
        return \Redirect::route('project.show', $boq->project_id);
    }

    public function destroy(Boq $boq)
    {
        $boq->delete();

        flash('Boq has been deleted', 'success');
        return \Redirect::route('project.show', $boq->project_id);
    }

    public function deleteAll(Project $project)
    {
        $items = Boq::where('project_id',$project->id)->get();
        foreach ($items as $item){
            $item->delete();
        }
        flash('All Boqs Deleted successfully', 'success');
        return \Redirect::route('project.show', $project->id);
    }

    function import(Project $project)
    {
        return view('boq.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $status = $this->dispatch(new BoqImportJob($project, $file->path()));
        if (count($status['failed'])) {
            $key = 'boq_' . time();
            \Cache::add($key, $status, 180);
            flash('Could not import all items', 'warning');
            return \Redirect::route('boq.fix-import', $key);
        }

        flash($status['success'] . ' BOQ items have been imported', 'success');
        return redirect()->route('project.show', $project);
    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('project.index');
        }

        $status = \Cache::get($key);

        return view('boq.fix-import', ['items' => $status['failed'], 'project' => Project::find($status['project_id']), 'key' => $key]);
    }

    function postFixImport(Request $request, $key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return \Redirect::route('project.index');
        }

        $data = $request->get('data');
        $errors = Boq::checkFixImport($data);
        if (!$errors) {
            $status = \Cache::get($key);

            foreach ($status['failed'] as $item) {
                if (isset($item['orig_unit_id']) && isset($data['units'][ $item['orig_unit_id'] ])) {
                    $item['unit'] = $data['units'][$item['orig_unit_id']];
                    UnitAlias::createAliasFor($item['unit'], $item['orig_unit_id']);
                }

                if (isset($item['orig_wbs_id']) && isset($data['wbs'][$item['orig_wbs_id']])) {
                    $item['wbs_id'] = $data['wbs'][$item['orig_wbs_id']];
                }

                Boq::create($item);
                ++$status['success'];
            }

            flash($status['success'] . ' BOQ items have been imported', 'success');
            return \Redirect::to(route('project.show', $status['project_id']) . '#boq');
        }

        flash('Could not import all items');
        return \Redirect::route('boq.fix-import', $key)->withErrors($errors)->withInput($request->all());
    }

    function exportBoq(Project $project)
    {
        $divisions = BoqDivision::whereHas('items', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })->get();

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Code');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Cost Account');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Description');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Discipline');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Unit');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Estimated Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Unit Price');
        $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Unit Dry');
        $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'KCC-Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Materials');
        $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'SubContractors');
        $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Man Power');
        $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'WBS-LEVEL');
        $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Division');
        $rowCount = 2;

        foreach ($divisions as $division) {
            if ($division->items->count()) {
                foreach ($division->items as $item) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $item->item_code);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $item->cost_account);

                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $item->description);

                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $item->type);

                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, isset(Unit::find($item->unit_id)->type) ? Unit::find($item->unit_id)->type : '');

                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $item->quantity);

                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $item->price_ur);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $item->dry_ur);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $item->kcc_qty);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $item->materials);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $item->subcon);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $item->subcon);
                    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, WbsLevel::find($item->wbs_id)->path);
                    $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, BoqDivision::find($item->division_id)->name);
                    $rowCount++;
                }
            }

        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $project->name . ' - BOQ.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

    function wipe(WipeRequest $request, Project $project)
    {
        $project->boqs()->delete();

        flash('All BOQs have been deleted', 'info');

        return \Redirect::to(route('project.show', $project) . '#boq');
    }
}
