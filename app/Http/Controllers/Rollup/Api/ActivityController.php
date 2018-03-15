<?php

namespace App\Http\Controllers\Rollup\Api;

use App\BreakdownResource;
use App\BreakDownResourceShadow;
use App\Formatters\RollupResourceFormatter;
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
            ->selectRaw('distinct cost_account as code, breakdown_id as id, wbs_id')
            ->where('is_rollup', false)->whereNull('rolled_up_at')
            ->get();

        $qty_surveys = Survey::whereIn('wbs_level_id', $wbsLevel->getParentIds())
            ->whereIn('cost_account', $cost_accounts->pluck('code'))
            ->select('cost_account', 'description')
            ->get()->keyBy('cost_account');

        return $cost_accounts->map(function($cost_account) use ($qty_surveys) {
            $survey =  $qty_surveys->get($cost_account->code);
            $cost_account->description = '';
            if ($survey) {
                $cost_account->description = $survey->description;
            }


            $cost_account->resources = BreakDownResourceShadow::where('wbs_id', $cost_account->wbs_id)
                ->canBeRolled()
                ->where('cost_account', $cost_account->code)->get()->map(function ($resource) {
                    return new RollupResourceFormatter($resource);
                });

            return $cost_account;
        });
    }
}
