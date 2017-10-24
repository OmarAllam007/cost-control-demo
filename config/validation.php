<?php

return [
    'boq' => [
        'project_id' => 'required',
        'wbs_id' => 'required|exists',
        'cost_account' => 'required|boq_unique',
        'item_code' => 'required|boq_unique',
    ],
    'qs' => []
];