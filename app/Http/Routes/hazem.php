<?php

Route::resource('project', 'ProjectController');
Route::resource('wbs-level', 'WbsLevelController');
Route::resource('std-activity', 'StdActivityController');
Route::resource('activity-division', 'ActivityDivisionController');
Route::resource('breakdown-template', 'BreakdownTemplateController');
Route::resource('std-activity-resource', 'StdActivityResourceController');

Route::resource('breakdown', 'BreakdownController');

Route::group(['prefix' => 'api'], function(){
    Route::get('breakdown-template', 'Api\BreakdownTemplateController@index');
    Route::get('std-activity-resource', 'Api\StdActivityResourceController@index');
    Route::get('cost-accounts', 'Api\CostAccountController@index');
    Route::get('cost-accounts/account', 'Api\CostAccountController@show');
    Route::get('resources', 'Api\ResourcesController@index');
    Route::get('productivity', 'Api\ProductivityController@index');
});

Route::group(['prefix' => 'wbs-level'], function () {
    Route::get('import/{project}', ['as' => 'wbs-level.import', 'uses' => 'WbsLevelController@import']);
    Route::post('import/{project}', ['as' => 'wbs-level.post-import', 'uses' => 'WbsLevelController@postImport']);
});