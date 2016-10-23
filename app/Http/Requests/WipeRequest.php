<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class WipeRequest extends Request
{
    public function authorize()
    {
        return \Auth::user()->can('wipe');
    }

    public function rules()
    {
        return [
            'wipe' => 'present|required'
        ];
    }
}
