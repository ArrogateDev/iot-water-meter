<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use Illuminate\Validation\Rule;

class AdminRequest extends BaseRequest
{

    public function rules()
    {
        $rules = [
            'name' => 'bail|required',
            'account' => [
                'bail',
                'required',
                Rule::unique('admins')
            ],
            'password' => 'nullable|bail|string|confirmed',
            'password_confirmation' => 'nullable|bail|string',
            'role_id' => 'nullable|int|exists:roles,id',
            'status' => 'bail|required|in:0,1',
        ];

        if ($this->method() === 'PUT') {
            $id = $this->segment(4);
            $rules['account'] = [
                'nullable',
                Rule::unique('admins')->ignore($id)
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => '请输入姓名',
            'account.required' => '请输入账号',
            'account.unique' => '账号已存在',
            'password.string' => '请输入密码',
            'password_confirmation.required' => '请输入确认密码',
            'password_confirmation.string' => '两次输入密码不一致',
            'role_id.int' => '角色格式错误',
            'role_id.exists' => '角色不存在',
            'status.required' => '状态格式错误',
            'status.in' => '状态格式错误',
        ];
    }
}
