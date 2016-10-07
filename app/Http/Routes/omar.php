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
    Route::get('/report',['as'=> 'productivity.report','uses'=>'ProductivityController@showReport']);
});

Route::group(['prefix' => 'unit'], function () {
    Route::post('/filter',['as'=>'unit.filter','uses'=>'UnitController@filter']);
});

Route::group(['prefix' => 'project'], function () {
    Route::get('wbs_report/{project}',['as'=>'wbs.report','uses'=>'ReportController@wbsReport']);
    Route::get('productivity_report/{project}',['as'=>'productivity.report','uses'=>'ReportController@productivityReport']);
    Route::get('standard_activity_report/{project}',['as'=>'stdActivity.report','uses'=>'ReportController@stdActivityReport']);

    Route::get('qs_summery_report/{project}',['as'=>'qsReport.report','uses'=>'ReportController@qsSummeryReport']);

    Route::get('resourse_dictionary/{project}',['as'=>'resource_dictionary.report','uses'=>'ReportController@resourceDictionary']);

    Route::get('man_power/{project}',['as'=>'man_power.report','uses'=>'ReportController@manPower']);
    Route::get('budget_summery/{project}',['as'=>'budget_summery.report','uses'=>'ReportController@budgetSummery']);
    Route::get('activity_resource_breakdown/{project}',['as'=>'activity_resource_breakdown.report','uses'=>'ReportController@activityResourceBreakDown']);
});

Route::resource('unit', 'UnitController');
Route::resource('survey', 'SurveyController');
Route::resource('business-partner', 'BusinessPartnerController');
Route::resource('resources', 'ResourcesController');
Route::resource('resource-type', 'ResourceTypeController');
Route::resource('boq', 'BoqController');
Route::resource('productivity', 'ProductivityController');
Route::resource('category', 'CategoryController');



