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
        $query = Boq::query();

        if ($request->has('term')) {
            $query->where('cost_account', 'like', '%' . $request->get('term') . '%');
        }

        if ($request->has('project')) {
            $query->where('project_id', $request->get('project'));
        }

        if ($request->has('wbs')) {
            $query->where('wbs_id', $request->get('wbs_id'));
        }

        return $query->orderBy('cost_account')
            ->take(20)
            ->pluck('cost_account');
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
