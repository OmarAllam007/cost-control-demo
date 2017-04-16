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
    return \Redirect::route('project.index');
});
//if (auth()->id() == 10) {
//    auth()->logout();
//}
Route::auth();
Route::get('auth/google', 'Auth\AuthController@googleRedirect');
Route::get('auth/google/continue', 'Auth\AuthController@googleHandle');

Route::group(['middleware' => 'auth'], function () {
    require __DIR__ . '/Routes/hazem.php';
    require __DIR__ . '/Routes/omar.php';
});


