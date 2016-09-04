<?php

Route::resource('unit', 'UnitController');
Route::resource('survey', 'SurveyController');
Route::resource('business-partner', 'BusinessPartnerController');
Route::resource('resources', 'ResourcesController');
Route::resource('resource-type', 'ResourceTypeController');
Route::resource('boq', 'BoqController');
Route::resource('productivity', 'ProductivityController');
Route::resource('category', 'CategoryController');


Route::get('/import',
    ['uses' => 'ActivityDivisionController@import'
        , 'as' => 'division.import'
    ]);
Route::get('/import',
    ['uses' => 'ProductivityController@import'
        , 'as' => 'productivity.import'
    ]);

Route::get('/importproductivity',
    ['uses' => 'ProductivityController@importProductivity'
        , 'as' => 'productivity.importProductivity'
    ]);

Route::get('/importcategory',
    ['uses' => 'CategoryController@importcategory'
        , 'as' => 'category.importcategory'
    ]);

Route::post('project/upload',
    ['uses' => 'ProjectController@upload'
        , 'as' => 'project.upload'
    ]);

Route::post('productivity/upload',
    ['uses' => 'ProductivityController@upload'
        , 'as' => 'productivity.upload'
    ]);

Route::resource('boq-division', 'BoqDivisionController');

Route::resource('csi-category', 'CsiCategoryController');


Route::group(['prefix' => 'boq'], function () {
    Route::get('import/{project}', ['as' => 'boq.import', 'uses' => 'BoqController@import']);
    Route::post('import/{project}', ['as' => 'boq.post-import', 'uses' => 'BoqController@postImport']);
});







