<?php

namespace App\Http\Controllers\Api;

use App\Boq;
use App\Survey;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class CostAccountController extends Controller
{
    function index(Request $request)
    {
        $query = Survey::query();

        if ($request->has('term')) {
            $query->where('qs_code', 'like', '%' . $request->get('term') . '%');
        }

        if ($request->has('project')) {
            $query->where('project_id', $request->get('project'));
        }

        if ($request->has('wbs')) {
            $query->where('wbs_level_id', $request->get('wbs_id'));
        }

        return $query->orderBy('qs_code')
            ->take(20)
            ->pluck('qs_code')
            ->filter()
            ->unique();
    }

    function show(Request $request)
    {
        if (!$request->has('account')) {
            return [];
        }

        $account = $request->get('account', 0);
        /** @var Survey $costAccount */
        $costAccount = Survey::where('cost_account', $account)->first();

        if (!$costAccount) {
            return [];
        }

        return ['budget_qty' => $costAccount->budget_qty, 'eng_qty' => $costAccount->eng_qty];
    }
}
