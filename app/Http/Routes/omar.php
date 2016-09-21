<?php





Route::resource('boq-division', 'BoqDivisionController');

Route::resource('csi-category', 'CsiCategoryController');


Route::group(['prefix' => 'boq'], function () {
    Route::get('import/{project}', ['as' => 'boq.import', 'uses' => 'BoqController@import']);
    Route::post('import/{project}', ['as' => 'boq.post-import', 'uses' => 'BoqController@postImport']);
});


Route::group(['prefix' => 'productivity'], function () {
    Route::get('import', ['as' => 'productivity.import', 'uses' => 'ProductivityController@import']);
    Route::post('import', ['as' => 'productivity.post-import', 'uses' => 'ProductivityController@postImport']);
});

Route::group(['prefix' => 'business-partner'], function () {
    Route::post('/filter',['as'=>'business-partner.filter','uses'=>'BusinessPartnerController@filter']);
});
Route::group(['prefix' => 'productivity'], function () {
    Route::post('/filter',['as'=>'productivity.filter','uses'=>'ProductivityController@filter']);
});


Route::resource('unit', 'UnitController');
Route::resource('survey', 'SurveyController');
Route::resource('business-partner', 'BusinessPartnerController');
Route::resource('resources', 'ResourcesController');
Route::resource('resource-type', 'ResourceTypeController');
Route::resource('boq', 'BoqController');
Route::resource('productivity', 'ProductivityController');
Route::resource('category', 'CategoryController');



