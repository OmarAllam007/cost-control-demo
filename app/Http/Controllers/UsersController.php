<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;


class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(25);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        $user->modules()->sync($request->get('module'));
        flash('User has been saved', 'success');
        return \Redirect::route('users.index');
    }


    public function show(User $user)
    {
        //
    }


    public function update(User $user, UserRequest $request)
    {
        $user->update($request->all());
        $user->modules()->sync($request->get('module'));
        flash('User has been saved', 'success');
        return \Redirect::route('users.index');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        flash('User has been deleted', true);
        return \Redirect::route('users.index');
    }
}
