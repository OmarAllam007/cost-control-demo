<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {
        $categories = Category::paginate();

        return view('category.index', compact('categories'));
    }

    public function create()
    {
        return view('category.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        Category::create($request->all());

        flash('Category has been saved', 'success');

        return \Redirect::route('category.index');
    }

    public function show(Category $category)
    {
        return view('category.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('category.edit', compact('category'));
    }

    public function update(Category $category, Request $request)
    {
        $this->validate($request, $this->rules);

        $category->update($request->all());

        flash('Category has been saved', 'success');

        return \Redirect::route('category.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        flash('Category has been deleted', 'success');

        return \Redirect::route('category.index');
    }

    public function importcategory()
    {
        $path = storage_path('files\survey_category.csv');
        $handle = fopen($path, "r");
        $parent_id = 0;
        if ($handle !== FALSE) {
            fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== FALSE) {
                $levels = count(array_filter($row));
                for ($counter = 0; $counter < $levels; $counter++) {
                    $row[$counter];
                    $category = Category::where('name', $row[$counter])->first();
                    if (is_null($category)) {
                        $category = Category::create([
                            'name' => $row[$counter],
                            'parent_id' => $parent_id,
                        ]);

                        $parent_id = $category->id;

                    } else {

                        $parent_id = $category->id;

                    }


                }

                $parent_id = 0;
            }

        }
        fclose($handle);
        return \Redirect::route('activity-division.index');
    }
}
