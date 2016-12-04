<?php

namespace App\Http\Requests;

class UserRequest extends Request
{
    public function authorize()
    {
        return $this->user()->is_admin;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ];

        if ($this->route()->hasParameter('user') && !$this->get('password')) {
            unset($rules['password']);
        }

        return $rules;
    }
}
