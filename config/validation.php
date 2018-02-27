<?php

return [
    'boq' => [
        'project_id' => 'required',
        'wbs_id' => 'required',
        'unit_id' => 'required',
        'cost_account' => 'required|boq_unique',
        'item_code' => 'required|boq_unique',
    ],

    'qty_survey' => [
        'item_code' => 'required|qs_has_boq',
        'description' => 'required',
        'qs_code' => 'required|qs_code_unique',
        'project_id' => 'required',
        'wbs_level_id' => 'required',
        'budget_qty' => 'required|gte:0',
        'eng_qty' => 'required|gte:0',
        'unit_id' => 'required'
    ],

    'breakdown' => [
        'wbs_level_id' => 'required',
        'std_activity_id' => 'required',
        'cost_account' => 'required|qs_code_found_on_wbs',
        'template_id' => 'required',
    ],

    'global_period' => [
        'start_date' => 'date|before:end_date',
        'end_date' => 'date|after:start_date',
        'spi_index' => 'numeric',
        'actual_progress' => 'numeric|min:0'
    ]
];