<?php

namespace App\Http\Controllers;

use App\Http\Requests\WipeRequest;
use App\Jobs\WbsImportJob;
use App\Project;
use App\WbsLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WbsLevelController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $wbsLevels = WbsLevel::tree()->paginate();

        return view('wbs-level.index', compact('wbsLevels'));
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

        return view('wbs-level.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $wbs_level = WbsLevel::create($request->all());

        flash('WBS level has been saved', 'success');

//        return \Redirect::route('project.show', $wbs_level->project_id);
        return \Redirect::to('/blank?reload=wbs');
    }

    public function show(WbsLevel $wbs_level)
    {
        return view('wbs-level.show', compact('wbs_level'));
    }

    public function edit(WbsLevel $wbs_level)
    {
        return view('wbs-level.edit', compact('wbs_level'));
    }

    public function update(WbsLevel $wbs_level, Request $request)
    {
        $this->validate($request, $this->rules);

        $wbs_level->update($request->all());

        flash('WBS level has been saved', 'success');

        return \Redirect::to('/blank?reload=wbs');
    }

    public function destroy(WbsLevel $wbs_level)
    {
        $wbs_level->deleteRecursive();

        flash('WBS level has been deleted', 'success');

        return \Redirect::route('project.show', $wbs_level->project_id);
    }

    function import(Project $project)
    {
        return view('wbs-level.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $count = $this->dispatch(new WbsImportJob($project, $file->path()));

        flash($count . 'WBS has been imported', 'success');
//        return redirect()->to(route('project.show', $project) . '#wbs-structure');
        return \Redirect::to('/blank?reload=wbs');
    }

    public function exportWbsLevels(Project $project)
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()
            ->getStyle('A1:D1')
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'FFFFCC')
                    )
                )
            );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'WBS-LEVEL 1');

        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'WBS-LEVEL 2');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'WBS-LEVEL 3');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'WBS-LEVEL 4');
        $rowCount = 2;
        foreach ($project->wbs_tree as $level) {
            $objPHPExcel->getActiveSheet()
                ->getStyle('A'.$rowCount.':D'.$rowCount)
                ->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'CCE5FF')
                        )
                    )
                );
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $level->name);
            $rowCount++;
            if ($level->children && $level->children->count()) {
                foreach ($level->children as $children) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $children->name);
                    $objPHPExcel->getActiveSheet()
                        ->getStyle('B'.$rowCount.':D'.$rowCount)
                        ->applyFromArray(
                            array(
                                'fill' => array(
                                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'FFE5CC')
                                )
                            )
                        );
                    $rowCount++;
                    if ($children->children && $children->children->count()) {
                        foreach ($children->children as $child) {
                            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $child->name);
                            $rowCount++;
                            if ($child->children && $child->children->count()) {
                                foreach ($child->children as $child) {
                                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $child->name);
                                    $rowCount++;
                                }
                            }
                        }
                    }

                }
            }

        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$project->name.' - WBS Levels.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

    function wipe(WipeRequest $request, Project $project)
    {
        $project->quantities()->delete();
        $project->boqs()->delete();
        $project->wbs_levels()->delete();

        \Cache::forget('wbs-tree-' . $project->id);

        $msg = 'WBS has been deleted';
        if ($request->ajax()) {
            return ['ok' => true, 'message' => $msg];
        }
        flash($msg, 'info');

        return \Redirect::route('project.show', $project);
    }
}
