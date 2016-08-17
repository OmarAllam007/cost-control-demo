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
}
