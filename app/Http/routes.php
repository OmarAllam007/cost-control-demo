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

Route::get('/', 'HomeController@index');
Route::get('home', 'HomeController@index')->name('home');
Route::get('budget', 'BudgetController@index')->name('home.budget');

Route::auth();
Route::get('auth/google', 'Auth\AuthController@googleRedirect');
Route::get('auth/google/continue', 'Auth\AuthController@googleHandle');

Route::group(['middleware' => 'auth'], function () {
    require __DIR__ . '/Routes/hazem.php';
    require __DIR__ . '/Routes/omar.php';

    Route::get('/breakdowns/import/{project}', ['as' => 'breakdowns.import', 'uses' => 'EasyUploadController@create']);
    Route::post('/breakdowns/import/{project}', ['as' => 'breakdowns.postImport', 'uses' => 'EasyUploadController@store']);
});

Route::get('/project/{project}/charter-data', 'ProjectCharterController@edit')->name('project.charter-data');
Route::patch('/project/{project}/charter-data', 'ProjectCharterController@update');
