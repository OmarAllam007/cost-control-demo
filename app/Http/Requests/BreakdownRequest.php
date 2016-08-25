<?php

namespace App\Http\Requests;

class BreakdownRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_id' => 'required',
            'wbs_level_id' => 'required',
            'std_activity_id' => 'required',
            'template_id' => 'required'
        ];
    }
}
