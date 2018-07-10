<?php

Route::get('/', 'ProjectController@index')->middleware('auth');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('auth/google', 'Auth\AuthController@googleRedirect');
Route::get('auth/google/continue', 'Auth\AuthController@googleHandle');

Route::group(['middleware' => 'auth'], function () {
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

        Route::group(['prefix' => 'cost'], function () {
            Route::get('breakdowns/{wbs_level}', 'Api\CostController@breakdowns');
            Route::get('resources/{project}', 'Api\CostController@resources');
            Route::get('activity-log/{wbs_level}', 'Api\CostController@activityLog');
            Route::get('batches/{project}', 'Api\CostController@batches');

            Route::delete('/delete-resource/{breakdown_resource}', 'Api\CostController@deleteResource');
            Route::delete('/delete-activity/{wbs}', 'Api\CostController@deleteActivity');
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

        Route::get('modify/{project}', 'Import\ImportSurveyController@edit')->name('survey.modify');
        Route::put('modify/{project}', 'Import\ImportSurveyController@update');

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

        Route::delete('delete-all', ['uses' => 'BreakdownTemplateController@deleteAll', 'as' => 'breakdown-template.deleteAll']);
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

        Route::post('copy-wbs/{source_wbs}/{target_wbs}', 'BreakdownResourceController@copy_wbs');
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

    Route::get('project/{project}/dashboard', 'CostReportsController@dashboard');
    Route::post('project/{project}/charts', 'CostReportsController@chart');

    Route::post('/concern/{project}', 'CostConcernsController@addConcernReport');

    Route::get('/project/{project}/issue-files', 'CostIssueFilesController@index');
    Route::get('/project/{project}/issue-files/create', 'CostIssueFilesController@create');
    Route::post('/project/{project}/issue-files', 'CostIssueFilesController@store');
    Route::get('/project/{project}/issue-files/{cost_issue_file}', 'CostIssueFilesController@show');
    Route::get('/project/{project}/issue-files/{cost_issue_file}/edit', 'CostIssueFilesController@edit');
    Route::patch('/project/{project}/issue-files/{cost_issue_file}', 'CostIssueFilesController@update');
    Route::delete('/project/{project}/issue-files/{cost_issue_file}', 'CostIssueFilesController@destroy');

    Route::get('/project/{project}/actual-revenue', 'ActualRevenueController@import');
    Route::post('/project/{project}/actual-revenue', 'ActualRevenueController@postImport');

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

    Route::get('/qty-survey/fix/{key}', 'FixQtySurveyBoqController@create')->name('qty-survey.fix-boq');
    Route::post('/qty-survey/fix/{key}', 'FixQtySurveyBoqController@store');

    Route::get('project/{project}/threshold', 'CostReportsController@threshold')->name('threshold-report');

    Route::get('/project/{project}/cost-man-days', 'CostManDaysController@create')->name('cost-man-days.import');
    Route::post('/project/{project}/cost-man-days', 'CostManDaysController@store')->name('cost-man-days.store');
    Route::get('/project/{project}/cost-man-days/export', 'CostManDaysController@show')->name('cost-man-days.export');

    Route::get('/projet/{project}/easy-upload', 'EasyUploadController@create')->name('easy-upload');
    Route::post('/projet/{project}/easy-upload', 'EasyUploadController@store');
    Route::get('/qty-survey/fix/{key}', 'FixQtySurveyBoqController@create')->name('qty-survey.fix-boq');
    Route::post('/qty-survey/fix/{key}', 'FixQtySurveyBoqController@store');

    Route::resource('roles', 'RolesController', ['parameters' => 'singular']);
    Route::get('project/{project}/roles', 'ProjectRolesController@edit')->name('project.roles');
    Route::put('project/{project}/roles', 'ProjectRolesController@update');

    Route::get('project/{project}/changelog', 'ChangelogController@show')->name('project.changelog');
    Route::get('/project/{project}/communication/budget', 'BudgetCommunicationController@create')->name('communication.budget');
    Route::post('/project/{project}/communication/budget', 'BudgetCommunicationController@store');


    Route::get('/project/{project}/communication/cost', 'CostCommunicationController@create')->name('communication.cost');
    Route::post('/project/{project}/communication/cost', 'CostCommunicationController@store');
    Route::get('/projet/{project}/easy-upload', 'EasyUploadController@create')->name('easy-upload');
    Route::post('/projet/{project}/easy-upload', 'EasyUploadController@store');
    Route::resource('global-periods', 'GlobalPeriodsController', ['parameters' => 'singular']);
    Route::get('project/{project}/changelog', 'ChangelogController@show')->name('project.changelog');
    Route::get('/rollup/create/{project}/{wbsLevel}/{stdActivity}', 'RollupController@create')->name('rollup.create');
    Route::post('/rollup/store/{key}', 'RollupController@store')->name('rollup.store');
    Route::get('project/{project}/changelog', 'ChangelogController@show')->name('project.changelog');

    Route::get('project/{project}/rollup-cost-account', 'Rollup\CostAccountRollupController@create');
    Route::post('project/{project}/rollup-cost-account', 'Rollup\CostAccountRollupController@store');

    Route::get('project/{project}/rollup-semi-cost-account', 'Rollup\SemiCostAccountRollupController@create');
    Route::post('project/{project}/rollup-semi-cost-account', 'Rollup\SemiCostAccountRollupController@store');

    Route::post('project/{project}/rollup-project-activity', 'Rollup\ActivityRollupController@store');
    Route::put('project/{project}/rollup-activity', 'Rollup\ActivityRollupController@update');

    Route::get('project/{project}/rollup-semi-activity', 'Rollup\SemiActivityRollupController@create');
    Route::post('project/{project}/rollup-semi-activity', 'Rollup\SemiActivityRollupController@store');

    Route::group(['prefix' => '/api/rollup/'], function () {
        Route::get('wbs/{wbsLevel}', 'Rollup\Api\WbsController@show');
        Route::get('activities/{wbsLevel}/{activity_id}', 'Rollup\Api\ActivityController@show');
        Route::get('activity-resources/{wbsLevel}', 'Rollup\Api\ActivityResourcesController@show');
        Route::get('cost-account/{wbsLevel}/{breakdown_id}', 'Rollup\Api\CostAccountController@show');

        Route::post('summarize/{wbs}/cost-account', 'Rollup\Api\CostAccountSumController@store');
        Route::post('summarize/{wbs}/activity', 'Rollup\Api\ActivitySumController@store');
    });

    Route::get('/project/{project}/modify-breakdown', 'ModifyBreakdownController@edit')->name('project.breakdown.import');
    Route::put('/project/{project}/modify-breakdown', 'ModifyBreakdownController@update')->name('project.breakdown.export');
    Route::get('/project/{project}/modify-breakdown/export', 'ModifyBreakdownController@index')->name('project.breakdown.export');

    Route::get('/dashboard/send', 'DashboardController@send');
    Route::post('/dashboard/send', 'DashboardController@postSend');

    Route::get('activity-log/{wbs}', 'ActivityLogController@show')->name('activity-log.show');
    Route::get('activity-log/{wbs}/excel', 'ActivityLogController@excel')->name('activity-log.excel');
    Route::get('api/activity-log/{wbs}', 'Api\ActivityLogController@show');

    Route::get('breakdown-template/import-to-project/{project}', 'ImportTemplateToProjectController@create')->name('breakdown-template.import-to-project');
    Route::post('breakdown-template/import-to-project/{project}', 'ImportTemplateToProjectController@store');

    Route::get('project/{project}/export-progress', 'UpdateProgressController@show')->name('project.export-progress');
    Route::get('project/{project}/update-progress', 'UpdateProgressController@create')->name('project.update-progress');
    Route::put('project/{project}/update-progress', 'UpdateProgressController@store');
    Route::get('project/{project}/modify-progress', 'UpdateProgressController@edit')->name('project.modify-progress');
    Route::put('project/{project}/modify-progress', 'UpdateProgressController@update');
    Route::get('/breakdowns/import/{project}', ['as' => 'breakdowns.import', 'uses' => 'EasyUploadController@create']);
    Route::post('/breakdowns/import/{project}', ['as' => 'breakdowns.postImport', 'uses' => 'EasyUploadController@store']);
    Route::get('/project/{project}/charter-data', 'ProjectCharterController@edit')->name('project.charter-data');
    Route::patch('/project/{project}/charter-data', 'ProjectCharterController@update');

    Route::resource('boq-division', 'BoqDivisionController');

    Route::resource('csi-category', 'CsiCategoryController');

    Route::group(['prefix' => 'csi-category'], function () {
        Route::delete('delete-all', ['uses' => 'CsiCategoryController@wipe', 'as' => 'csi-category.wipe']);
    });
    Route::group(['prefix' => 'boq'], function () {
        Route::get('import/{project}', ['as' => 'boq.import', 'uses' => 'BoqController@import']);
        Route::post('import/{project}', ['as' => 'boq.post-import', 'uses' => 'BoqController@postImport']);
        Route::get('export/{project}', ['as' => 'boq.export', 'uses' => 'BoqController@exportBoq']);
        Route::delete('delete-all/{project}', ['as' => 'boq.delete-all', 'uses' => 'BoqController@deleteAll']);

    });
    Route::group(['prefix', 'costcontrol'], function () {
        Route::get('export/{project}', ['as' => 'costshadow.export', 'uses' => 'ActualMaterialController@ExportCostBreakdown']);
    });
    Route::group(['prefix' => 'breakdown'], function () {
        Route::get('export/{project}', ['as' => 'break_down.export', 'uses' => 'BreakdownController@exportBreakdown']);
        Route::get('printAll/{project}', ['as' => 'break_down.printall', 'uses' => 'BreakdownController@printAll']);
    });

    Route::group(['prefix' => 'productivity'], function () {
        Route::get('import', ['as' => 'productivity.import', 'uses' => 'ProductivityController@import']);
        Route::post('import', ['as' => 'productivity.post-import', 'uses' => 'ProductivityController@postImport']);
        Route::get('export/{project}', ['as' => 'productivity.export', 'uses' => 'ProductivityController@exportProductivity']);
        Route::delete('delete-all', ['uses' => 'ProductivityController@wipe', 'as' => 'productivity.wipe']);
        Route::get('export-all-productivities/', ['uses' => 'ProductivityController@exportPublicProductivities', 'as' => 'productivity.exportAll']);
        Route::get('modify/', ['as' => 'all-productivities.modify', 'uses' => 'ProductivityController@modifyAllProductivities']);
        Route::post('modify/', ['as' => 'all-productivities.post-modify', 'uses' => 'ProductivityController@postModifyAllProductivities']);
    });


    Route::group(['prefix' => 'business-partner'], function () {
        Route::post('/filter', ['as' => 'business-partner.filter', 'uses' => 'BusinessPartnerController@filter']);
        Route::delete('delete-all', ['uses' => 'BusinessPartnerController@wipe', 'as' => 'partner.wipe']);
    });

    Route::group(['prefix' => 'productivity'], function () {
        Route::post('/filter', ['as' => 'productivity.filter', 'uses' => 'ProductivityController@filter']);
        Route::get('/report', ['as' => 'productivity.report', 'uses' => 'ProductivityController@showReport']);
    });

    Route::group(['prefix' => 'unit'], function () {
        Route::post('/filter', ['as' => 'unit.filter', 'uses' => 'UnitController@filter']);
        Route::delete('delete-all', ['uses' => 'UnitController@wipe', 'as' => 'unit.wipe']);
    });
//reports budget cost
    Route::group(['prefix' => 'project'], function () {
        Route::get('projectInfo/{project}', ['uses' => 'CostReportsController@projectInformation', 'as' => 'cost_control.info']);
        Route::get('cost_summary/{project}', ['uses' => 'CostReportsController@costSummary', 'as' => 'cost_control.cost-summary']);
        Route::get('cost_significant_materials/{project}', ['uses' => 'CostReportsController@significantMaterials', 'as' => 'cost.significant']);
        Route::get('cost_standard_activity/{project}', ['uses' => 'CostReportsController@standardActivity', 'as' => 'cost.standard_activity_report']);
        Route::get('cost_boq/{project}', ['uses' => 'CostReportsController@boqReport', 'as' => 'cost.boq_report']);
        Route::get('cost_resource_code/{project}', ['uses' => 'CostReportsController@resourceCodeReport', 'as' => 'cost.resource_code_report']);
        Route::get('cost_overdraft/{project}', ['uses' => 'CostReportsController@overdraftReport', 'as' => 'cost.overdraft']);
        Route::get('cost_activity/{project}', ['uses' => 'CostReportsController@activityReport', 'as' => 'cost.activity_report']);
        Route::get('cost_resource_dictionary/{project}', ['uses' => 'CostReportsController@resourceDictionaryReport', 'as' => 'cost.dictionary']);
        Route::get('variance_analysis/{project}', ['uses' => 'CostReportsController@varianceAnalysisReport', 'as' => 'cost.variance']);
        Route::get('reports/{project}', ['as' => 'project.reports', 'uses' => 'ReportController@getReports']);
        Route::get('wbs_report/{project}', ['as' => 'wbs.report', 'uses' => 'ReportController@wbsReport']);
        Route::get('productivity_report/{project}', ['as' => 'productivity.report', 'uses' => 'ReportController@productivityReport']);
        Route::get('standard_activity_report/{project}', ['as' => 'stdActivity.report', 'uses' => 'ReportController@stdActivityReport']);

        Route::get('resourse_dictionary/{project}', ['as' => 'resource_dictionary.report', 'uses' => 'ReportController@resourceDictionary']);
        Route::get('man_power/{project}', ['as' => 'man_power.report', 'uses' => 'ReportController@manPower']);

        Route::get('budget-summary/{project}', ['as' => 'budget_summary.report', 'uses' => 'ReportController@budgetSummary']);

        Route::get('activity_resource_breakdown/{project}', ['as' => 'activity_resource_breakdown.report', 'uses' => 'ReportController@activityResourceBreakDown']);

        Route::get('qs_summary_report/{project}', ['as' => 'qsReport.report', 'uses' => 'ReportController@qsSummary']);

        Route::get('budget_cost_dry_cost/{project}', ['as' => 'budget_cost_dry_cost.report', 'uses' => 'ReportController@budgetCostVSDryCostByBuilding']);

        Route::get('budget_cost_by_resource_type/{project}', ['as' => 'budget_cost_vs_break_down.report', 'uses' => 'ReportController@budgetCostByResourceType']);

        Route::get('budget_cost_by_discipline/{project}', ['as' => 'budget_cost_by_discipline.report', 'uses' => 'ReportController@budgetCostDiscipline']);

        Route::get('budget_cost_by_building/{project}', ['as' => 'budget_cost_by_building.report', 'uses' => 'ReportController@budgetCostByBuilding']);

        Route::get('budget_cost_dry_cost_discipline/{project}', ['as' => 'budget_cost_dry_cost_discipline.report', 'uses' => 'ReportController@budgetCostDryCostDiscipline']);

        Route::get('qty_cost_discipline/{project}', ['as' => 'qty_cost_discipline.report', 'uses' => 'ReportController@quantityAndCostByDiscipline']);

        Route::get('revised_boq/{project}', ['as' => 'revised_boq.report', 'uses' => 'ReportController@revisedBoq']);

        Route::get('boq_price_list/{project}', ['as' => 'boq_price_list.report', 'uses' => 'ReportController@boqPriceList']);

        Route::get('high-priority/{project}', ['as' => 'high_priority.report', 'uses' => 'ReportController@highPriorityMaterials']);

        Route::get('wbs-dictionary/{project}', 'ReportController@wbsDictionary')->name('wbs_dictionary_report');
        Route::get('wbs-labours/{project}', 'ReportController@wbsLabours')->name('wbs_labours_report');
        //Export Project Reports
        Route::get('export_std_activity/{project}', ['as' => 'budget_std_activity.export', 'uses' => 'ExportReportController@exportStdActivity']);
        Route::get('export_productivity/{project}', ['as' => 'budget_productivity.export', 'uses' => 'ExportReportController@exportProductivity']);

        //Modify Boq
        Route::get('modify_boq/{project}', ['as' => 'boq.modify', 'uses' => 'BoqController@modifyProjectBoqs']);
        Route::post('modify_boq/', ['as' => 'boq.post-modify', 'uses' => 'BoqController@postModifyProjectBoqs']);


        Route::get('show_productivity_report/{project}', ['as' => 'productivity-cost-show.modify', 'uses' => 'CostReportsController@productivityReport']);
        Route::get('show_issues/{project}', ['as' => 'show_issues.report', 'uses' => 'CostReportsController@issuesReport']);

        Route::get('{project}/budget-trend', 'ReportController@budgetTrend')->name('project.budget-trend');
        Route::get('{project}/profitability', 'ReportController@profitability')->name('project.profitability-report');
        Route::get('{project}/charter', 'ReportController@charter')->name('project.charter-report');
        Route::get('{project}/check-list', 'ReportController@check_list')->name('project.budget-checklist');
        Route::get('{project}/comparison', 'ReportController@comparison_report')->name('project.comparison');

        Route::get('{project}/waste-index', 'CostReportsController@wasteIndexReport')->name('project.waste-index-report');
        Route::get('{project}/productivity-index', 'CostReportsController@productivityIndexReport')->name('project.productivity-index-report');
    });
    Route::get('/download_trend/{id}/download', 'ProductivityController@downloadTrend');
    Route::get('/download_labor_trend/{id}/download', 'ProductivityController@downloadLaborTrend');

//reports cost control


//export resports
    Route::group(['prefix' => 'project'], function () {
        Route::get('wbs_levels/export/{project}', ['as' => 'wbs_report.export', 'uses' => 'ExportReportController@exportWbsReport']);
    });

    Route::group(['prefix' => 'project'], function () {
        Route::get('activity-mapping/export/{project}', ['as' => 'activity_mapping.export', 'uses' => 'ActivityMapController@exportActivityMapping']);
        Route::get('resource-mapping/export/{project}', ['as' => 'resource_mapping.export', 'uses' => 'ResourcesController@exportResourceMapping']);
    });

    Route::group(['prefix' => 'survey'], function () {
        Route::get('export/{project}', ['as' => 'survey.export', 'uses' => 'SurveyController@exportQuantitySurvey']);
        Route::get('dublicate/{key}', ['as' => 'survey.dublicate', 'uses' => 'SurveyController@dublicateQuantitySurvey']);
        Route::post('dublicate/{key}', ['as' => 'survey.post-dublicate', 'uses' => 'SurveyController@postDublicateQuantitySurvey']);
    });


    Route::group(['prefix' => 'resources'], function () {
        Route::get('export/{project}', ['as' => 'resources.export', 'uses' => 'ResourcesController@exportResources']);
        Route::get('export-all-resources/', ['as' => 'all_resources.export', 'uses' => 'ResourcesController@exportAllResources']);
        Route::get('modify/', ['as' => 'all-resources.modify', 'uses' => 'ResourcesController@modifyAllResources']);
        Route::post('modify/', ['as' => 'all-resources.post-modify', 'uses' => 'ResourcesController@postModifyAllResources']);
        Route::delete('deleteAll/{project}', ['uses' => 'ResourcesController@projectWipeAll', 'as' => 'project-resources.wipeAll']);
        Route::get('export-cost-resources/{project}', ['as' => 'resources-cost.export', 'uses' => 'ResourcesController@exportCostResources']);


    });
    Route::group(['prefix' => 'resource-type'], function () {
        Route::delete('delete-all', ['uses' => 'ResourceTypeController@wipe', 'as' => 'type.wipe']);
    });
    Route::group(['prefix' => 'project'], function () {
        Route::get('financial-period/{project}', ['uses' => 'FinancialPeriodController@index', 'as' => 'financial.index']);

        Route::get('financial-period/{project}/create', ['uses' => 'FinancialPeriodController@create', 'as' => 'financial.create']);
        Route::post('financial-period/{project}/store', ['uses' => 'FinancialPeriodController@store', 'as' => 'financial.store']);
    });
    Route::delete('/wbs-level/reources/{id}', ['uses' => 'BreakdownController@wpsdelete', 'as' => 'wbsresource.delete']);
    Route::group(['prefix' => 'breakdown-resource'], function () {
        Route::delete('/delete-all/{project}', ['uses' => 'BreakdownResourceController@deleteAllBreakdowns', 'as' => 'breakdownresources.deleteAllBreakdowns']);
    });

//Route::group(['prefix'=>'actual-revenue','as'=>'actual-revenue.import'],function (){
//   Route::get('import/{project}','ActualRevenueController@import');
//   Route::post('import/{project}','ActualRevenueController@postImport');
//});
    Route::group(['prefix' => 'cost-productivity', 'as' => 'productivity-report.import'], function () {
        Route::get('import/{project}', 'ProductivityController@importReport');
        Route::post('import/{project}', 'ProductivityController@postImportReport');
    });

    Route::group(['prefix' => 'cost-labor', 'as' => 'cost-labor.import'], function () {
        Route::get('import/{project}', 'ProductivityController@laborImportReport');
        Route::post('import/{project}', 'ProductivityController@laborPostImportReport');
    });
    Route::get('export-std-activity-report-budget/{project}', ['uses' => 'StdActivityController@exportStdActivityBudgetReport', 'as' => 'export.budget_std_Activity']);
    Route::get('export-productivity-report-budget/{project}', ['uses' => 'ProductivityController@exportProductivityReport', 'as' => 'export.budget_productivity']);

    Route::resource('unit', 'UnitController');
    Route::resource('survey', 'SurveyController');
    Route::resource('business-partner', 'BusinessPartnerController');
    Route::resource('resources', 'ResourcesController');
    Route::resource('resource-type', 'ResourceTypeController');
    Route::resource('boq', 'BoqController');
    Route::resource('productivity', 'ProductivityController');
    Route::resource('category', 'CategoryController');
    Route::resource('category', 'CategoryController');
});

