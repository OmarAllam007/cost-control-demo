<?php

namespace App\Http\Controllers;

use App\Category;
use App\Jobs\QuantitySurveyImportJob;
use App\Project;
use App\Survey;
use App\Unit;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    protected $rules = [
        'description' => 'required', 'cost_account' => 'required',
        'project_id' => 'required', 'wbs_level_id' => 'required',
        'budget_qty' => 'required', 'eng_qty' => 'required',
    ];

    public function index()
    {
        $surveys = Survey::paginate();
        return view('survey.index', compact('surveys'));
    }

    public function create(Request $request)
    {
        if (!$request->has('project')) {
            return \Redirect::route('project.index');
        }

        if (!Project::find($request->get('project'))) {
            return \Redirect::route('project.index');
        }

        return view('survey.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $survey = Survey::create($request->all());

        flash('Quantity survey has been saved', 'success');

        return \Redirect::route('project.show', $survey->project_id);
    }

    public function show(Survey $survey)
    {
        return view('survey.show', compact('survey', 'units'));
    }

    public function edit(Survey $survey)
    {
        return view('survey.edit', compact('survey'));
    }

    public function update(Survey $survey, Request $request)
    {
        $this->validate($request, $this->rules);

        $survey->update($request->all());
        $survey->syncVariables($request->get('variables'));

        flash('Quantity survey has been saved', 'success');

        return \Redirect::route('project.show', $survey->project_id);
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        flash('Quantity survey has been deleted', 'success');

        return \Redirect::route('project.show', $survey->project_id);
    }

    function import(Project $project)
    {
        return view('survey.import', compact('project'));
    }

    function postImport(Project $project, Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx',
        ]);

        $file = $request->file('file');

        $status = $this->dispatch(new QuantitySurveyImportJob($project, $file->path()));

        if ($status['failed']->count()) {
            $key = 'qs_import_' . time();
            \Cache::add($key, $status, 180);
            flash('Could not import the following items. Please fix.', 'warning');
            return redirect()->route('survey.fix-import', $key);
        }

        flash($status . ' Quantity survey items have been imported', 'success');
        return redirect()->route('project.show', $project);
    }

    function fixImport($key)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return redirect()->route('project.index');
        }

        $status = \Cache::get($key);
        $project = Project::find($status['project_id']);
        $items = $status['failed'];
        return view('survey.fix-import', compact('items', 'key', 'project'));
    }

    function postFixImport($key, Request $request)
    {
        if (!\Cache::has($key)) {
            flash('Nothing to fix');
            return redirect()->route('project.index');
        }

        $status = \Cache::get($key);
        $project = Project::find($status['project_id']);
        $data = $request->get('data');
        $errors = Survey::checkImportData($data);

        if (!$errors) {
            /** @var Project $project */
            $units = $data['units'];
            $wbs = $data['wbs'];

            foreach ($status['failed'] as $key => $item) {
                if (!$item['unit_id']) {
                    $item['unit_id'] = $units[ $item['unit'] ];
                }

                if (!$item['wbs_level_id']) {
                    $item['wbs_level_id'] = $wbs[ $item['wbs_code'] ];
                }

                $project->quantities()->create($item);
                ++$status['success'];
            }

            flash($status . ' Quantity survey items have been imported', 'success');
            return redirect()->route('project.show', $project);
        }

        flash('Could not import some items.', 'warning');
        return \Redirect::back()->withErrors($errors)->withInput(compact('data'));
    }


    public function exportQuantitySurvey(Project $project)
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Cost Account');
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'WBS-Level');
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Description');
        $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Budget Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Engineer Quantity');
        $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Unit');

        $rowCount = 2;
        foreach ($project->quantities as $quantity) {
            $cost_account = $quantity->cost_account;
            $wbs_level = $quantity->wbsLevel->path;
            $description = $quantity->description;
            $budget_qty = $quantity->budget_qty;
            $eng_qty = $quantity->eng_qty;

            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $cost_account);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $wbs_level);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $description);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $budget_qty);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $eng_qty);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, Unit::find($quantity->unit_id)->type);
            $rowCount++;

        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $project->name . ' - Quantity Survey.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
    }

}