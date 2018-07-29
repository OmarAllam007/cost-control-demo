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

Route::auth();
Route::get('auth/google', 'Auth\AuthController@googleRedirect');
Route::get('auth/google/continue', 'Auth\AuthController@googleHandle');

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('acknowledgement', 'HomeController@acknowledgement')->name('home.acknowledgement');
    Route::get('budget', 'BudgetController@index')->name('home.budget');
    Route::get('reports', 'HomeController@reports')->name('home.reports');
    Route::get('master-data', 'HomeController@masterData')->name('home.master-data');
    Route::get('cost-control', 'CostControlController@index')->name('home.cost-control');
    Route::get('coming-soon', 'HomeController@comingSoon')->name('home.coming-soon');

    Route::get('project/{project}/reports', 'ProjectReportsController@show')->name('project.reports');


    require __DIR__ . '/Routes/hazem.php';
    require __DIR__ . '/Routes/omar.php';

    Route::get('/breakdowns/import/{project}', ['as' => 'breakdowns.import', 'uses' => 'EasyUploadController@create']);
    Route::post('/breakdowns/import/{project}', ['as' => 'breakdowns.postImport', 'uses' => 'EasyUploadController@store']);
});

Route::get('/project/{project}/charter-data', 'ProjectCharterController@edit')->name('project.charter-data');
Route::patch('/project/{project}/charter-data', 'ProjectCharterController@update');
