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
});