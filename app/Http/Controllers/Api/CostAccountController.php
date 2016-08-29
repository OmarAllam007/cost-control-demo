<?php

namespace App\Http\Controllers\Api;

use App\Survey;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class CostAccountController extends Controller
{
    function index(Request $request)
    {
        $query = Survey::query();

        if ($request->has('term')) {
            $query->where('cost_account', 'like', '%' . $request->get('term') . '%');
        }

        return $query->orderBy('cost_account')->pluck('cost_account');
    }
}
