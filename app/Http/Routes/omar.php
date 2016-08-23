<?php

Route::resource('unit', 'UnitController');
Route::resource('survey','SurveyController');
Route::resource('business-partner', 'BusinessPartnerController');
Route::resource('resources', 'ResourcesController');
Route::resource('resource-type', 'ResourceTypeController');


Route::resource('productivity', 'ProductivityController');

Route::resource('category', 'CategoryController');

Route::get('/import',
    ['uses'=>'ActivityDivisionController@import'
    ,'as'=>'division.import'
    ]);


