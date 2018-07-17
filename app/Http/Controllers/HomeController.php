<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home.index');
    }

    function acknowledgement()
    {
        return view('home.acknowledgement');
    }

    function comingSoon()
    {
        return view('home.coming-soon');
    }

    function reports()
    {
        return view('home.reports');
    }
}
