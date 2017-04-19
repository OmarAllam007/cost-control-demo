<?php
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
Route::group(['prefix','costcontrol'],function (){
   Route::get('export/{project}',['as'=>'costshadow.export','uses'=>'ActualMaterialController@ExportCostBreakdown']);
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
    Route::get('projectInfo/{project}',['uses'=>'CostReportsController@projectInformation','as'=>'cost_control.info']);
    Route::get('cost_summery/{project}',['uses'=>'CostReportsController@costSummery','as'=>'cost_control.cost-summery']);
    Route::get('cost_significant_materials/{project}',['uses'=>'CostReportsController@significantMaterials','as'=>'cost.significant']);
    Route::get('cost_standard_activity/{project}',['uses'=>'CostReportsController@standardActivity','as'=>'cost.standard_activity_report']);
    Route::get('cost_boq/{project}',['uses'=>'CostReportsController@boqReport','as'=>'cost.boq_report']);
    Route::get('cost_resource_code/{project}',['uses'=>'CostReportsController@resourceCodeReport','as'=>'cost.resource_code_report']);
    Route::get('cost_overdraft/{project}',['uses'=>'CostReportsController@overdraftReport','as'=>'cost.overdraft']);
    Route::get('cost_activity/{project}',['uses'=>'CostReportsController@activityReport','as'=>'cost.activity_report']);
    Route::get('cost_resource_dictionary/{project}',['uses'=>'CostReportsController@resourceDictionaryReport','as'=>'cost.dictionary']);
    Route::get('variance_analysis/{project}',['uses'=>'CostReportsController@varianceAnalysisReport','as'=>'cost.variance']);
    Route::get('reports/{project}', ['as' => 'project.reports', 'uses' => 'ReportController@getReports']);
    Route::get('wbs_report/{project}', ['as' => 'wbs.report', 'uses' => 'ReportController@wbsReport']);
    Route::get('productivity_report/{project}', ['as' => 'productivity.report', 'uses' => 'ReportController@productivityReport']);
    Route::get('standard_activity_report/{project}', ['as' => 'stdActivity.report', 'uses' => 'ReportController@stdActivityReport']);

    Route::get('resourse_dictionary/{project}', ['as' => 'resource_dictionary.report', 'uses' => 'ReportController@resourceDictionary']);
    Route::get('man_power/{project}', ['as' => 'man_power.report', 'uses' => 'ReportController@manPower']);

    Route::get('budget_summery/{project}', ['as' => 'budget_summery.report', 'uses' => 'ReportController@budgetSummery']);

    Route::get('activity_resource_breakdown/{project}', ['as' => 'activity_resource_breakdown.report', 'uses' => 'ReportController@activityResourceBreakDown']);

    Route::get('qs_summery_report/{project}', ['as' => 'qsReport.report', 'uses' => 'ReportController@qsSummery']);

    Route::get('budget_cost_dry_cost/{project}', ['as' => 'budget_cost_dry_cost.report', 'uses' => 'ReportController@budgetCostVSDryCost']);

    Route::get('budget_cost_vs_break_down/{project}', ['as' => 'budget_cost_vs_break_down.report', 'uses' => 'ReportController@budgetCostVSBreadDown']);

    Route::get('budget_cost_by_discipline/{project}', ['as' => 'budget_cost_by_discipline.report', 'uses' => 'ReportController@budgetCostDiscipline']);

    Route::get('budget_cost_by_building/{project}', ['as' => 'budget_cost_by_building.report', 'uses' => 'ReportController@budgetCostForBuilding']);

    Route::get('budget_cost_dry_cost_discipline/{project}', ['as' => 'budget_cost_dry_cost_discipline.report', 'uses' => 'ReportController@budgetCostDryCostDiscipline']);

    Route::get('qty_cost_discipline/{project}', ['as' => 'qty_cost_discipline.report', 'uses' => 'ReportController@quantityAndCostByDiscipline']);

    Route::get('revised_boq/{project}', ['as' => 'revised_boq.report', 'uses' => 'ReportController@revisedBoq']);

    Route::get('boq_price_list/{project}', ['as' => 'boq_price_list.report', 'uses' => 'ReportController@boqPriceList']);

    Route::get('high_priority/{project}', ['as' => 'high_priority.report', 'uses' => 'ReportController@highPriority']);
    Route::post('high_priority_resources/{project}', ['as' => 'generate_top_matrial_reports.report', 'uses' => 'ReportController@topMaterialResources']);
    Route::post('high_priority_resources_reset/{project}', ['as' => 'reset_top_matrial_reports.report', 'uses' => 'ReportController@topMaterialResourcesReset']);


    //Export Project Reports
    Route::get('export_std_activity/{project}',['as'=>'budget_std_activity.export','uses'=>'ExportReportController@exportStdActivity']);
    Route::get('export_productivity/{project}',['as'=>'budget_productivity.export','uses'=>'ExportReportController@exportProductivity']);

    //Modify Boq
    Route::get('modify_boq/{project}',['as'=>'boq.modify','uses'=>'BoqController@modifyProjectBoqs']);
    Route::post('modify_boq/',['as'=>'boq.post-modify','uses'=>'BoqController@postModifyProjectBoqs']);



    Route::get('show_productivity_report/{project}',['as'=>'productivity-cost-show.modify','uses'=>'CostReportsController@productivityReport']);
    Route::get('show_issues/{project}',['as'=>'show_issues.report','uses'=>'CostReportsController@issuesReport']);

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
    Route::delete('deleteAll/{project}',['uses'=>'ResourcesController@projectWipeAll','as'=>'project-resources.wipeAll']);
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
Route::group(['prefix'=>'cost-productivity','as'=>'productivity-report.import'],function (){
    Route::get('import/{project}','ProductivityController@importReport');
    Route::post('import/{project}','ProductivityController@postImportReport');
});

Route::group(['prefix'=>'cost-labor','as'=>'cost-labor.import'],function (){
    Route::get('import/{project}','ProductivityController@laborImportReport');
    Route::post('import/{project}','ProductivityController@laborPostImportReport');
});
Route::get('export-std-activity-report-budget/{project}',['uses'=>'StdActivityController@exportStdActivityBudgetReport','as'=>'export.budget_std_Activity']);
Route::get('export-productivity-report-budget/{project}',['uses'=>'ProductivityController@exportProductivityReport','as'=>'export.budget_productivity']);

Route::resource('unit', 'UnitController');
Route::resource('survey', 'SurveyController');
Route::resource('business-partner', 'BusinessPartnerController');
Route::resource('resources', 'ResourcesController');
Route::resource('resource-type', 'ResourceTypeController');
Route::resource('boq', 'BoqController');
Route::resource('productivity', 'ProductivityController');
Route::resource('category', 'CategoryController');
Route::resource('category', 'CategoryController');

//comment

//Export Budget Reports

