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
        'project_id' => 'required',
        'wbs_level_id' => 'required',
        'budget_qty' => 'required|gte:0',
        'eng_qty' => 'required|gte:0',
        'unit_id' => 'required'
    ]
];