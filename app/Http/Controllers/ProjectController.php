<?php

namespace App\Http\Controllers;

use App\ActivityDivision;
use App\Project;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use PHPExcel;


class ProjectController extends Controller
{


    protected $rules = ['name' => 'unique:projects|required'];

    public function index()
    {
        $projects = Project::paginate();

        return view('project.index', compact('projects'));
    }

    public function create()
    {
        return view('project.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        Project::create($request->all());

        flash('Project has been saved', 'success');

        return \Redirect::route('project.index');
    }

    public function show(Project $project)
    {
        $project->load(['wbs_levels', 'breakdown_resources']);
        return view('project.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('project.edit', compact('project'));
    }

    public function update(Project $project, Request $request)
    {
        $this->validate($request, $this->rules);

        $project->update($request->all());

        flash('Project has been saved', 'success');

        return \Redirect::route('project.index');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        flash('Project has been deleted', 'success');

        return \Redirect::route('project.index');
    }


    public function upload(Request $request)
    {

        try {
//upload file
            $original_name = $request->file('file')->getClientOriginalName();
            $request->file('file')->move(
                base_path() . '/storage/files/', $original_name
            );
//EO-Upload File

//Read file .....
            $input = base_path() . '/storage/files/' . $original_name;
            $inputFileType = \PHPExcel_IOFactory::identify($input);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($input);
        } //if file not found
        catch (Exception $e) {
            die('Error loading file "' . pathinfo($input, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        //iterate over sheet


        /**@var \PHPExcel_Worksheet $objWorksheet */
        $objWorksheet = $objPHPExcel->getActiveSheet();


        foreach ($objWorksheet->getRowIterator(2) as $row) {

            /** @var \PHPExcel_Worksheet_Row $row */
            $cellIterator = $row->getCellIterator();

            $cellIterator->setIterateOnlyExistingCells(false);
            $inputs = [];

            foreach ($cellIterator as $cell) {
                $inputs[] = $cell->getValue();
            }
            $project = Project::where('name', $inputs[1])->first();

            if (is_null($project)) {//if project is exist
                Project::create([
                    'name' => $inputs[1],
                    'description' => $inputs[2]
                ]);
            } else {

                continue;
            }


        }

        return redirect()->back();
    }


}