<?php

namespace App\Http\Controllers;

use App\Report;
use App\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    function __construct()
    {
        $this->middleware('admin');
    }

    function index()
    {
        $roles = Role::orderBy('name')->paginate();
        return view('roles.index', compact('roles'));
    }

    function create()
    {
        $reports = Report::all()->groupBy('type');

        return view('roles.create', compact('reports'));
    }

    function store(Request $request)
    {
        $this->validate($request, ['name' => 'required', 'reports' => 'required']);

        $role = Role::create($request->only('name', 'description'));
        $role->reports()->sync($request->input('reports'));

        flash('Role has been saved', 'success');
        return \Redirect::route('roles.index');
    }

    function edit(Role $role)
    {
        $reports = Report::all()->groupBy('type');

        return view('roles.edit', compact('role', 'reports'));
    }

    function update(Role $role, Request $request)
    {
        $this->validate($request, ['name' => 'required', 'reports' => 'required']);

        $role->update($request->only('name', 'description'));
        $role->reports()->sync($request->input('reports'));

        flash('Role has been saved', 'success');
        return \Redirect::route('roles.index');
    }

    function delete(Role $role)
    {
        $role->delete();

        flash('Role has been deleted');

        return \Redirect::route('role.index');
    }
}
