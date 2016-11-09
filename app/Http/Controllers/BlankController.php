<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class BlankController extends Controller
{
    function index()
    {
        return view('blank.index');
    }
}
