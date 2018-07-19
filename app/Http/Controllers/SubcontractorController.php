<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class SubcontractorController extends Controller
{
    function index()
    {
        return view('home.subcontractors');
    }
}
