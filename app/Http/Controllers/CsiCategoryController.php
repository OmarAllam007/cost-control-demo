<?php

namespace App\Http\Controllers;

use App\CsiCategory;
use App\Http\Requests\WipeRequest;
use Illuminate\Http\Request;

class CsiCategoryController extends Controller
{

    protected $rules = ['name' => 'required'];

    public function index()
    {

        $categories = CsiCategory::tree()->orderBy('name')->paginate();

        return view('csi-category.index', compact('categories'));
    }

    public function create()
    {
        return view('csi-category.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);

        CsiCategory::create($request->all());

        flash('Csi category has been saved', 'success');

        return \Redirect::route('csi-category.index');
    }

    public function show(CsiCategory $csi_category)
    {
        return view('csi-category.show', compact('csi_category'));
    }

    public function edit(CsiCategory $csi_category)
    {
        return view('csi-category.edit', compact('csi_category'));
    }

    public function update(CsiCategory $csi_category, Request $request)
    {
        $this->validate($request, $this->rules);

        $csi_category->update($request->all());

        flash('Csi category has been saved', 'success');

        return \Redirect::route('csi-category.index');
    }

    public function destroy(CsiCategory $csi_category)
    {
        $csi_category->delete();

        flash('Csi category has been deleted', 'success');

        return \Redirect::route('csi-category.index');
    }

    function wipe(WipeRequest $request)
    {
        \DB::table('csi_categories')->delete();
        flash('All categories have been deleted', 'info');
        return \Redirect::route('csi-category.index');
    }
}
