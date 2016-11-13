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

    public function forbiddenResponse()
    {
        $msg = 'You are not authorized to wipe';

        if ($this->ajax()) {
            return ['ok' => false, 'message' => $msg];
        }

        flash($msg);
        return \Redirect::back();
    }


}
