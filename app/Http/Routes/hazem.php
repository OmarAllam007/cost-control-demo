<?php

Route::group(['prefix' => 'api'], function(){
    Route::get('breakdown-template', 'Api\BreakdownTemplateController@index');
    Route::get('std-activity-resource', 'Api\StdActivityResourceController@index');
    Route::get('cost-accounts', 'Api\CostAccountController@index');
    Route::get('cost-accounts/account', 'Api\CostAccountController@show');
    Route::get('resources', 'Api\ResourcesController@index');
    Route::get('productivity', 'Api\ProductivityController@index');
    Route::get('productivity/labours-count/{productivity}', 'Api\ProductivityController@labors_count');
    Route::get('std-activity/variables/{std_activity}', 'Api\StdActivityController@variables');

    Route::group(['prefix' => 'wbs'], function(){
        Route::get('/{project}', 'Api\WbsController@index');
        Route::get('breakdowns/{wbs_level}', 'Api\WbsController@breakdowns');
        Route::get('boq/{wbs_level}', 'Api\WbsController@boq');
        Route::get('qty-survey/{wbs_level}', 'Api\WbsController@qtySurvey');
        Route::get('tree-by-resource/{project}', 'Api\WbsController@tree_by_resource');
        Route::get('tree-by-wbs/{project}', 'Api\WbsController@tree_by_wbs');
    });
});

Route::group(['prefix' => 'wbs-level'], function () {
    Route::get('import/{project}', ['as' => 'wbs-level.import', 'uses' => 'WbsLevelController@import']);
    Route::post('import/{project}', ['as' => 'wbs-level.post-import', 'uses' => 'WbsLevelController@postImport']);
    Route::get('export/{project}',['as'=>'wbs-level.export','uses'=>'WbsLevelController@exportWbsLevels']);

    Route::delete('wipe/{project}', ['as' => 'wbs-level.wipe', 'uses' => 'WbsLevelController@wipe']);
});

Route::group(['prefix' => 'resources'], function () {
    Route::get('import', ['as' => 'resources.import', 'uses' => 'ResourcesController@import']);
    Route::post('import', ['as' => 'resources.post-import', 'uses' => 'ResourcesController@postImport']);
    Route::get('fix-import/{key}', ['as' => 'resources.fix-import', 'uses' => 'ResourcesController@fixImport']);
    Route::post('fix-import/{key}', ['as' => 'resources.post-fix-import', 'uses' => 'ResourcesController@postFixImport']);

    Route::get('override/{resources}/{project}', ['as' => 'resources.override', 'uses' => 'ResourcesController@override']);
    Route::post('override/{resources}/{project}', ['as' => 'resources.post-override', 'uses' => 'ResourcesController@postOverride']);

    Route::post('/filter', ['as' => 'resources.filter', 'uses' => 'ResourcesController@filter']);

    Route::delete('wipe', ['as' => 'resources.wipe', 'uses' => 'ResourcesController@wipe']);

    Route::get('import-codes', ['as' => 'resources.import-codes', 'uses' => 'ResourcesController@importCodes']);
    Route::post('import-codes', ['as' => 'resources.post-import-codes', 'uses' => 'ResourcesController@postImportCodes']);
});

Route::group(['prefix' => 'productivity'], function () {
    Route::get('override/{productivity}/{project}', ['as' => 'productivity.override', 'uses' => 'ProductivityController@override']);
    Route::post('override/{productivity}/{project}', ['as' => 'productivity.post-override', 'uses' => 'ProductivityController@postOverride']);

    Route::get('fix-import/{key}', ['as' => 'productivity.fix-import', 'uses' => 'ProductivityController@fixImport']);
    Route::post('fix-import/{key}', ['as' => 'productivity.post-fix-import', 'uses' => 'ProductivityController@postFixImport']);
});

Route::group(['prefix' => 'std-activity'], function () {
    Route::get('import', ['as' => 'std-activity.import', 'uses' => 'StdActivityController@import']);
    Route::post('import', ['as' => 'std-activity.post-import', 'uses' => 'StdActivityController@postImport']);

    Route::post('filters', ['as' => 'std-activity.filters', 'uses' => 'StdActivityController@filters']);
    Route::delete('wipe', ['as' => 'std-activity.wipe', 'uses' => 'StdActivityController@wipe']);

    Route::get('export-all-stdActivities/',['uses' => 'StdActivityController@exportAllActivities', 'as' => 'std-activity.exportAll']);

});

Route::group(['prefix' => 'survey'], function () {
    Route::get('import/{project}', ['as' => 'survey.import', 'uses' => 'SurveyController@import']);
    Route::post('import/{project}', ['as' => 'survey.post-import', 'uses' => 'SurveyController@postImport']);
    Route::get('fix-import/{code}', ['as' => 'survey.fix-import', 'uses' => 'SurveyController@fixImport']);
    Route::post('fix-import/{code}', ['as' => 'survey.post-fix-import', 'uses' => 'SurveyController@postFixImport']);

    Route::delete('wipe/{project}', ['as' => 'survey.wipe', 'uses' => 'SurveyController@wipe']);
});

Route::group(['prefix' => 'breakdown-template'], function () {
    Route::post('filters', ['as' => 'breakdown-template.filters', 'uses' => 'BreakdownTemplateController@filters']);

    Route::get('import', ['as' => 'breakdown-template.import', 'uses' => 'BreakdownTemplateController@import']);
    Route::post('import', ['as' => 'breakdown-template.post-import', 'uses' => 'BreakdownTemplateController@postImport']);
});

Route::group(['prefix' => 'boq'], function() {
    Route::get('fix-import/{key}', ['as' => 'boq.fix-import', 'uses' => 'BoqController@fixImport']);
    Route::post('fix-import/{key}', ['as' => 'boq.post-fix-import', 'uses' => 'BoqController@postFixImport']);

    Route::delete('wipe/{project}', ['as' => 'boq.wipe', 'uses' => 'BoqController@wipe']);
});

Route::group(['prefix' => 'breakdown'], function(){
    Route::get('duplicate/{breakdown}', ['as' => 'breakdown.duplicate', 'uses' => 'BreakdownController@duplicate']);
    Route::post('duplicate/{breakdown}', ['as' => 'breakdown.post-duplicate', 'uses' => 'BreakdownController@postDuplicate']);

    Route::post('filters/{project}', ['as' => 'breakdown.filters', 'uses' => 'BreakdownController@filters']);
    Route::delete('wipe/{project}', ['as' => 'breakdown.wipe', 'uses' => 'BreakdownResourceController@wipe']);
});

Route::group(['prefix' => 'project', 'as' => 'project.'], function () {
    Route::get('budget/{project}', ['as' => 'budget', 'uses' => 'ProjectController@show']);
    Route::get('cost-control/{project}', ['as' => 'cost-control', 'uses' => 'ProjectController@costControl']);
});

Route::group(['prefix' => 'activity-map', 'as' => 'activity-map.'], function () {
    Route::get('import/{project}', ['as' => 'import', 'uses' => 'ActivityMapController@import']);
    Route::post('import/{project}', ['as' => 'post-import', 'uses' => 'ActivityMapController@postImport']);
});

Route::resource('project', 'ProjectController');
Route::resource('wbs-level', 'WbsLevelController');
Route::resource('std-activity', 'StdActivityController');
Route::resource('activity-division', 'ActivityDivisionController');
Route::resource('breakdown-template', 'BreakdownTemplateController');
Route::resource('std-activity-resource', 'StdActivityResourceController');
Route::resource('breakdown', 'BreakdownController');
Route::resource('breakdown-resource', 'BreakdownResourceController');
Route::resource('period', 'PeriodController');

Route::get('/blank', 'BlankController@index');