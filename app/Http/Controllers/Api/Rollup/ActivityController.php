<?php

namespace App\Http\Controllers\Api\Rollup;

use App\BreakDownResourceShadow;
use App\Survey;
use App\WbsLevel;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    function show(WbsLevel $wbsLevel, $activity_id)
    {
        $this->authorize('cost_owner', $wbsLevel->project);

        $cost_accounts = BreakDownResourceShadow::where('wbs_id', $wbsLevel->id)
            ->where('activity_id', $activity_id)
            ->selectRaw('distinct cost_account as code, breakdown_id as id')
            ->get();

        $qty_survies = Survey::whereIn('wbs_level_id', $wbsLevel->getParentIds())
            ->whereIn('cost_account', $cost_accounts->pluck('code'))
            ->select('cost_account', 'description')
            ->get()->keyBy('cost_account');

        return $cost_accounts->map(function($cost_account) use ($qty_survies) {
            $survey =  $qty_survies->get($cost_account->code);
            $cost_account->description = '';
            if ($survey) {
                $cost_account->description = $survey->description;
            }
            return $cost_account;
        });
    }
}
