<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    $projects = \App\Project::paginate();
    return view('project.index',['projects'=>$projects]);
});

Route::auth();

require __DIR__ . '/Routes/hazem.php';
require __DIR__ . '/Routes/omar.php';


Route::resource('?', '?Controller');



