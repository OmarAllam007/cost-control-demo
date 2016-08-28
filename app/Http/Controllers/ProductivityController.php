<?php
namespace App\Http\Controllers;

use App\ActivityDivision;
use App\CSI_category;
use App\Productivity;
use App\Unit;
use Illuminate\Http\Request;

class ProductivityController extends Controller
{

    protected $rules = ['' => ''];

    public function index()
    {
        $productivities = Productivity::paginate();

        return view('productivity.index', compact('productivities'));
    }

    public function create()
    {
        $csi_category = CSI_category::lists('name', 'id')->all();
        $units_drop = Unit::lists('type', 'id')->all();


        return view('productivity.create', compact('csi_category', 'units_drop'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        $this->after_reduction = ($request->reduction_factor * $request->daily_output) + $request->daily_output;
        Productivity::create($request->all());

        flash('Productivity has been saved', 'success');

        return \Redirect::route('productivity.index');
    }

    public function show(Productivity $productivity)
    {
        return view('productivity.show', compact('productivity'));
    }

    public function edit(Productivity $productivity)
    {
        $csi_category = CSI_category::lists('name', 'id')->all();
        $units_drop = Unit::lists('type', 'id')->all();

        return view('productivity.edit', compact('productivity', 'units_drop', 'csi_category'));
    }

    public function update(Productivity $productivity, Request $request)
    {
        $this->validate($request, $this->rules);
        $productivity->after_reduction = ($request->reduction_factor * $request->daily_output) + $request->daily_output;
        $productivity->update($request->all());

        flash('Productivity has been saved', 'success');

        return \Redirect::route('productivity.index');
    }

    public function destroy(Productivity $productivity)
    {
        $productivity->delete();

        flash('Productivity has been deleted', 'success');

        return \Redirect::route('productivity.index');
    }

    public function import()
    {
        set_time_limit(60);
        $start = microtime(true);
        $path = storage_path('files\category.csv');
        $handle = fopen($path, "r");

        if ($handle !== FALSE) {
            fgetcsv($handle);
            $productivity_category = CSI_category::query()->pluck('name', 'id')->toArray();

            while (($row = fgetcsv($handle)) !== FALSE) {
                $levels = array_filter($row);
                $parent_id = 0;
                foreach ($levels as $level) { //fill categories
                    if (!isset($productivity_category[$level])) {
                        $category = CSI_category::create([
                            'name' => $level,
                            'parent_id' => $parent_id,
                        ]);

                        $productivity_category[$level] = $parent_id = $category->id;

                    } else {
                        $parent_id = $productivity_category[$level];
                    }
                }
                //fill productivies

            }


        }

        fclose($handle);
        return view('productivity.index');

    }


    public function importProductivity()
    {
        set_time_limit(60);
        Productivity::truncate();
        $path = storage_path('files\items.csv');
        $handle = fopen($path, "r");

        if ($handle !== FALSE) {
            fgetcsv($handle);
            $productivity_category = CSI_category::query()->pluck('id', 'name')->toArray();
            $unit = Unit::query()->pluck('id', 'type')->toArray();


            while (($row = fgetcsv($handle)) !== FALSE) {
//                $units = Unit::where('type',$row[1])->first();
//                if(is_null($units)){
//                    if($row[1]=='' || $row[1]==',,'||$row[1]=='"'||$row[1]==' '){
//
//                    }
//                    else {
//                        Unit::create([
//                            'type' => $row[1],
//                        ]);
//                    }
//                }//
                if (isset($productivity_category) && isset($unit)) {
                    while (($row = fgetcsv($handle)) !== FALSE) {


                        $item = Productivity::create([
                            'csi_category_id' => $productivity_category[$row[0]],
                            'unit' => isset($unit[$row[1]]) ? $unit[$row[1]] : 0,
                            'crew_structure' => $row[2],
                            'crew_hours' => $row[3],
                            'crew_equip' => $row[4],
                            'daily_output' => $row[5],
                            'man_hours' => $row[6],
                            'equip_hours' => $row[7],
                            'reduction_factor' => $row[8],
                            'after_reduction' => $row[9],
                            'source' => $row[10]
                        ]);
                    }


                } else {
                    echo 'error';
                }
            }
        }
        fclose($handle);

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

        Productivity::truncate();


        foreach ($objWorksheet->getRowIterator(2) as $row) {

            /** @var \PHPExcel_Worksheet_Row $row */
            $cellIterator = $row->getCellIterator();

            $cellIterator->setIterateOnlyExistingCells(false);
            $inputs = [];

            foreach ($cellIterator as $cell) {
                $inputs[] = $cell->getValue();

            }

            Productivity::create([

                'unit' => $inputs[2],
                'crew_structure' => isset($inputs[3])?$inputs[3]:'',
                'crew_hours' => $inputs[4],
                'crew_equip' => $inputs[5],
                'daily_output' => $inputs[6],
                'man_hours' => $inputs[7],
                'equip_hours' => $inputs[8],
                'reduction_factor' => $inputs[9],
                'after_reduction' => $inputs[10],
                'source' => $inputs[11],
            ]);

        }

        return redirect()->back();
    }


}
