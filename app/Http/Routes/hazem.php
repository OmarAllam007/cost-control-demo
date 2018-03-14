<?php

Route::group(['prefix' => 'api'], function () {
    Route::get('breakdown-template', 'Api\BreakdownTemplateController@index');
    Route::get('breakdown-template/template/{project}', 'Api\BreakdownTemplateController@templates');
    Route::get('std-activity-resource', 'Api\StdActivityResourceController@index');
    Route::get('cost-accounts', 'Api\CostAccountController@index');
    Route::get('cost-accounts/account', 'Api\CostAccountController@show');
    Route::get('resources', 'Api\ResourcesController@index');
    Route::get('productivity', 'Api\ProductivityController@index');
    Route::get('productivity/labours-count/{productivity}', 'Api\ProductivityController@labors_count');
    Route::get('std-activity/variables/{std_activity}', 'Api\StdActivityController@variables');
    Route::get('resources/resources/{project}', 'Api\ResourcesController@Resources');
    Route::get('productivities/productivity/{project}', 'Api\ProductivityController@productivities');

    Route::group(['prefix' => 'wbs'], function () {
        Route::get('/{project}', 'Api\WbsController@index');
        Route::get('breakdowns/{wbs_level}', 'Api\WbsController@breakdowns');
        Route::get('boq/{wbs_level}', 'Api\WbsController@boq');
        Route::get('qty-survey/{wbs_level}', 'Api\WbsController@qtySurvey');
        Route::get('tree-by-resource/{project}', 'Api\WbsController@tree_by_resource');
        Route::get('tree-by-wbs/{project}', 'Api\WbsController@tree_by_wbs');
    });

    Route::group(['prefix' => 'cost'], function() {
        Route::get('breakdowns/{wbs_level}', 'Api\CostController@breakdowns');
        Route::get('resources/{project}', 'Api\CostController@resources');
        Route::get('activity-log/{wbs_level}', 'Api\CostController@activityLog');
        Route::get('batches/{project}', 'Api\CostController@batches');

        Route::delete('/delete-resource/{breakdown_resource}', 'Api\CostController@deleteResource');
        Route::delete('/delete-activity/{breakdown}', 'Api\CostController@deleteActivity');
        Route::delete('/delete-wbs/{wbs_level}', 'Api\CostController@deleteWbs');
        Route::delete('/delete-current/{project}', 'Api\CostController@deleteProject');
        Route::delete('/delete-batch/{actual_batch}', 'Api\CostController@deleteBatch');
    });
});

Route::group(['prefix' => 'wbs-level'], function () {
    Route::get('import/{project}', ['as' => 'wbs-level.import', 'uses' => 'WbsLevelController@import']);
    Route::post('import/{project}', ['as' => 'wbs-level.post-import', 'uses' => 'WbsLevelController@postImport']);
    Route::get('export/{project}', ['as' => 'wbs-level.export', 'uses' => 'WbsLevelController@exportWbsLevels']);

    Route::delete('wipe/{project}', ['as' => 'wbs-level.wipe', 'uses' => 'WbsLevelController@wipe']);
    Route::get('{wbs_level}/copy-to-project', 'CopyWbsController@create');
    Route::post('{wbs_level}/copy-to-project', 'CopyWbsController@store');
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
    Route::get('fix-import-codes/{key}', ['as' => 'resources.fix-import-codes', 'uses' => 'ResourcesController@fixImportCodes']);
    Route::post('fix-import-codes/{key}', ['as' => 'resources.post-fix-import-codes', 'uses' => 'ResourcesController@postFixImportCodes']);
    Route::delete('delete-codes/{project}', ['as' => 'resources.delete-codes', 'uses' => 'ResourceCodeController@delete']);
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

    Route::get('export-all-stdActivities/', ['uses' => 'StdActivityController@exportAllActivities', 'as' => 'std-activity.exportAll']);
    Route::get('modify/', ['as' => 'all-stdActivites.modify', 'uses' => 'StdActivityController@modifyAllActivities']);
    Route::post('modify/', ['as' => 'all-stdActivites.post-modify', 'uses' => 'StdActivityController@postModifyAllActivities']);

    Route::get('dublicate', ['as' => 'std-activity.dublicated', 'uses' => 'StdActivityController@dublicateActivity']);
});

Route::group(['prefix' => 'survey'], function () {
    Route::get('import/{project}', ['as' => 'survey.import', 'uses' => 'SurveyController@import']);
    Route::post('import/{project}', ['as' => 'survey.post-import', 'uses' => 'SurveyController@postImport']);
    Route::get('fix-import/{code}', ['as' => 'survey.fix-import', 'uses' => 'SurveyController@fixImport']);
    Route::post('fix-import/{code}', ['as' => 'survey.post-fix-import', 'uses' => 'SurveyController@postFixImport']);

    Route::delete('wipe/{project}', ['as' => 'survey.wipe', 'uses' => 'SurveyController@wipe']);
});

Route::group(['prefix' => 'breakdown-template'], function () {
    Route::get('export', 'BreakdownTemplateExportModifyController@index')->name('breakdown-template.export');
    Route::get('modify', 'BreakdownTemplateExportModifyController@edit')->name('breakdown-template.modify');
    Route::put('modify', 'BreakdownTemplateExportModifyController@update');

    Route::post('filters', ['as' => 'breakdown-template.filters', 'uses' => 'BreakdownTemplateController@filters']);

    Route::get('import', ['as' => 'breakdown-template.import', 'uses' => 'BreakdownTemplateController@import']);
    Route::post('import', ['as' => 'breakdown-template.post-import', 'uses' => 'BreakdownTemplateController@postImport']);

    Route::delete('delete-all',['uses'=>'BreakdownTemplateController@deleteAll' , 'as'=>'breakdown-template.deleteAll']);
});

Route::group(['prefix' => 'boq'], function () {
    Route::get('fix-import/{key}', ['as' => 'boq.fix-import', 'uses' => 'BoqController@fixImport']);
    Route::post('fix-import/{key}', ['as' => 'boq.post-fix-import', 'uses' => 'BoqController@postFixImport']);

    Route::delete('wipe/{project}', ['as' => 'boq.wipe', 'uses' => 'BoqController@wipe']);
});

Route::group(['prefix' => 'breakdown'], function () {
    Route::get('duplicate/{breakdown}', ['as' => 'breakdown.duplicate', 'uses' => 'BreakdownController@duplicate']);
    Route::post('duplicate/{breakdown}', ['as' => 'breakdown.post-duplicate', 'uses' => 'BreakdownController@postDuplicate']);

    Route::post('filters/{project}', ['as' => 'breakdown.filters', 'uses' => 'BreakdownController@filters']);
    Route::delete('wipe/{wbs_level}', ['as' => 'breakdown.wipe', 'uses' => 'BreakdownResourceController@wipe']);

    Route::get('copy-wbs/{source_wbs}/{target_wbs}', 'BreakdownResourceController@copy_wbs');
});

Route::group(['prefix' => 'project', 'as' => 'project.'], function () {
    Route::get('budget/{project}', ['as' => 'budget', 'uses' => 'ProjectController@show']);
    Route::get('cost-control/{project}', ['as' => 'cost-control', 'uses' => 'ProjectController@costControl']);
    Route::get('{project}/duplicate', ['as' => 'duplicate', 'uses' => 'ProjectController@duplicate']);
});

Route::group(['prefix' => 'activity-map', 'as' => 'activity-map.'], function () {
    Route::get('import/{project}', ['as' => 'import', 'uses' => 'ActivityMapController@import']);
    Route::post('import/{project}', ['as' => 'post-import', 'uses' => 'ActivityMapController@postImport']);
    Route::get('fix-import/{project}/{key}', ['as' => 'fix-import', 'uses' => 'ActivityMapController@fixImport']);
    Route::post('fix-import/{project}/{key}', ['as' => 'post-fix-import', 'uses' => 'ActivityMapController@postFixImport']);
    Route::delete('delete/{project}', ['as' => 'delete', 'uses' => 'ActivityMapController@delete']);
});

Route::group(['prefix' => 'actual-material', 'as' => 'actual-material.'], function () {
    Route::get('import/{project}', ['as' => 'import', 'uses' => 'ActualMaterialController@import']);
    Route::get('mapping/{actual_batch}', ['as' => 'mapping', 'uses' => 'ActualMaterialController@fixMapping']);
    Route::get('multiple/{actual_batch}', ['as' => 'multiple', 'uses' => 'ActualMaterialController@fixMultiple']);
    Route::get('units/{actual_batch}', ['as' => 'units', 'uses' => 'ActualMaterialController@fixunits']);
    Route::get('progress/{actual_batch}', ['as' => 'progress', 'uses' => 'ActualMaterialController@progress']);
    Route::get('status/{actual_batch}', ['as' => 'status', 'uses' => 'ActualMaterialController@status']);
    Route::get('resources/{actual_batch}', ['as' => 'resources', 'uses' => 'ActualMaterialController@resources']);
    Route::get('closed/{actual_batch}', ['as' => 'closed', 'uses' => 'ActualMaterialController@closed']);

    Route::post('import/{project}', ['as' => 'post-import', 'uses' => 'ActualMaterialController@postImport']);
    Route::post('mapping/{actual_batch}', ['as' => 'post-mapping', 'uses' => 'ActualMaterialController@postFixMapping']);
    Route::post('multiple/{actual_batch}', ['as' => 'post-multiple', 'uses' => 'ActualMaterialController@postFixMultiple']);
    Route::post('units/{actual_batch}', ['as' => 'post-units', 'uses' => 'ActualMaterialController@postFixUnits']);
    Route::post('progress/{actual_batch}', ['as' => 'post-progress', 'uses' => 'ActualMaterialController@postProgress']);
    Route::post('status/{actual_batch}', ['as' => 'post-status', 'uses' => 'ActualMaterialController@postStatus']);
    Route::post('resources/{actual_batch}', ['as' => 'post-resources', 'uses' => 'ActualMaterialController@postResources']);
    Route::post('closed/{actual_batch}', ['as' => 'post-closed', 'uses' => 'ActualMaterialController@postClosed']);
});

Route::group(['prefix' => 'cost', 'as' => 'cost.'], function () {
    Route::get('{cost_shadow}/edit', ['as' => 'edit', 'uses' => 'CostController@edit']);
    Route::get('{breakdown_resource}/pseudo-edit', ['as' => 'pseudo-edit', 'uses' => 'CostController@pseudoEdit']);
    Route::post('{cost_shadow}', ['as' => 'update', 'uses' => 'CostController@update']);

    Route::get('{project}/old-data', ['as' => 'old-data', 'uses' => 'CostController@importOldData']);
    Route::post('{project}/old-data', ['as' => 'post-old-data', 'uses' => 'CostController@postImportOldData']);
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
Route::resource('users', 'UsersController', ['parameters' => 'singular']);
Route::get('/actual-batches/{actual_batch}', 'ActualBatchesController@show');
Route::get('/actual-batches/{actual_batch}/download', 'ActualBatchesController@download');
Route::get('/actual-batches/{actual_batch}/excel', 'ActualBatchesController@excel');

Route::get('/blank', 'BlankController@index');
Route::get('dashboard', 'DashboardController@index');

Route::get('/summary', function() {
    return App\CostShadow::joinBudget('budget.resource_type')->sumFields([
        'cost.to_date_cost', 'cost.allowable_ev_cost', 'budget.budget_cost'
    ])->get();
});

Route::get('project/{project}/dashboard', 'CostReportsController@dashboard');
Route::post('project/{project}/charts', 'CostReportsController@chart');

Route::post('/concern/{project}','CostConcernsController@addConcernReport');

Route::get('/project/{project}/issue-files', 'CostIssueFilesController@index');
Route::get('/project/{project}/issue-files/create', 'CostIssueFilesController@create');
Route::post('/project/{project}/issue-files', 'CostIssueFilesController@store');
Route::get('/project/{project}/issue-files/{cost_issue_file}', 'CostIssueFilesController@show');
Route::get('/project/{project}/issue-files/{cost_issue_file}/edit', 'CostIssueFilesController@edit');
Route::patch('/project/{project}/issue-files/{cost_issue_file}', 'CostIssueFilesController@update');
Route::delete('/project/{project}/issue-files/{cost_issue_file}', 'CostIssueFilesController@destroy');

Route::get('/project/{project}/actual-revenue','ActualRevenueController@import');
Route::post('/project/{project}/actual-revenue','ActualRevenueController@postImport');

Route::get('project/{project}/revisions', ['as' => 'revisions.index', 'uses' => 'BudgetRevisionsController@index']);
Route::get('project/{project}/revisions/create', ['as' => 'revisions.create', 'uses' => 'BudgetRevisionsController@create']);
Route::get('project/{project}/revisions/{revision}', ['as' => 'revisions.show', 'uses' => 'BudgetRevisionsController@show']);
Route::get('project/{project}/revisions/{revision}/export', ['as' => 'revisions.export', 'uses' => 'BudgetRevisionsController@export']);
Route::post('project/{project}/revisions', ['as' => 'revisions.store', 'uses' => 'BudgetRevisionsController@store']);
Route::delete('project/{project}/revisions/{revision}/delete', 'BudgetRevisionsController@destroy');
Route::get('project/{project}/revisions/{revision}/edit', ['as' => 'revisions.edit', 'uses' => 'BudgetRevisionsController@edit']);
Route::put('project/{project}/revisions/{revision}', ['as' => 'revisions.update', 'uses' => 'BudgetRevisionsController@update']);

Route::post('/period-report/{period}', 'PeriodReportsController@store')->name('period-report.store');


Route::get('/template-resource/{project}/create/{breakdown_template}', 'TemplateResourceImpactController@create')->name('template-resource.create');
Route::post('/template-resource/{project}/create/{breakdown_template}', 'TemplateResourceImpactController@store')->name('template-resource.store');
Route::get('/template-resource/{project}/edit/{template_resource}', 'TemplateResourceImpactController@edit')->name('template-resource.edit');
Route::put('/template-resource/{project}/{template_resource}', 'TemplateResourceImpactController@update')->name('template-resource.update');
Route::patch('/template-resource/{project}/{template_resource}', 'TemplateResourceImpactController@update')->name('template-resource.update');
Route::get('/template-resource/{project}/delete/{template_resource}', 'TemplateResourceImpactController@delete')->name('template-resource.delete');
Route::delete('/template-resource/{project}/{template_resource}', 'TemplateResourceImpactController@destroy')->name('template-resource.destroy');

Route::get('project/{project}/modify-productivity', 'ProjectProductivityController@edit')->name('project.modify-productivity');
Route::post('project/{project}/modify-productivity', 'ProjectProductivityController@update')->name('project.modify-productivity');
Route::get('project/{project}/failed-productivity', 'ProjectProductivityController@show')->name('project.failed-productivity');

Route::get('project/{project}/threshold', 'CostReportsController@threshold')->name('threshold-report');

Route::get('/project/{project}/cost-man-days', 'CostManDaysController@create')->name('cost-man-days.import');
Route::post('/project/{project}/cost-man-days', 'CostManDaysController@store')->name('cost-man-days.store');
Route::get('/project/{project}/cost-man-days/export', 'CostManDaysController@show')->name('cost-man-days.export');

Route::get('/qty-survey/fix/{key}', 'FixQtySurveyBoqController@create')->name('qty-survey.fix-boq');
Route::post('/qty-survey/fix/{key}', 'FixQtySurveyBoqController@store');

Route::resource('global-periods', 'GlobalPeriodsController', ['parameters' => 'singular']);
Route::get('project/{project}/changelog', 'ChangelogController@show')->name('project.changelog');

Route::get('/project/{project}/modify-breakdown', 'ModifyBreakdownController@edit')->name('project.breakdown.import');
Route::put('/project/{project}/modify-breakdown', 'ModifyBreakdownController@update')->name('project.breakdown.export');
Route::get('/project/{project}/modify-breakdown/export', 'ModifyBreakdownController@index')->name('project.breakdown.export');