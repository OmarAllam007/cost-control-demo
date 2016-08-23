<?php

namespace App\Http\Controllers;


use App\Breakdown;
use Illuminate\Http\Request;

class BreakdownController extends Controller
{

    public function create()
    {
        return view('breakdown.create');
    }

    public function store(Request $request)
    {

    }

    public function edit(Breakdown $breakdown)
    {
        return view('breakdown.edit', compact('breakdown'));
    }

    public function update(Request $request, Breakdown $breakdown)
    {

    }

    public function delete(Breakdown $breakdown)
    {

    }
}