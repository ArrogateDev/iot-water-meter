<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseCode;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\Manage\Authority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PersonalController extends Controller
{
    /**
     * 获取信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        $admin = $request->user('admin');
        $admin->load(['role:id,name']);

        $result['name'] = $admin->name;
        $result['avatar'] = asset($admin->avatar) ? Storage::disk('public')->url($admin->avatar) : '';
        $result['account'] = $admin->account;
        $result['roles'] = [$admin->role->name];

        return $this->responseSuccess($result);
    }

    /**
     * 修改信息
     *
     * @param AdminRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiException
     */
    public function update(Request $request)
    {
        $admin = $request->user('admin');
        $inputs = $request->only(['name', 'mobile', 'old_password', 'password', 'password_confirmation']);

        $validator = Validator::make($inputs, [
            'name' => 'bail|required',
            'mobile' => 'bail|required',
            'old_password' => 'nullable|bail|size:32|current_password:admin',
            'password' => 'nullable|bail|string||size:32|confirmed',
            'password_confirmation' => 'nullable|bail|string|size:32',
        ], [
            'name.unique' => '请输入名称',
            'mobile.unique' => '请输入手机号',
            'old_password.size' => '原密码密码错误',
            'old_password.current_password' => '原密码密码错误',
            'password.string' => '密码格式错误',
            'password.size' => '密码格式错误',
            'password.confirmed' => '请输入确认密码',
            'password_confirmation.string' => '确认密码格式错误',
            'password_confirmation.size' => '确认密码格式错误',
        ]);

        if ($validator->fails()) {
            throw new ApiException($validator->errors()->first(), ResponseCode::PARAM_ERR);
        }

        unset($inputs['old_password'], $inputs['password_confirmation']);
        foreach ($inputs as $key => $value) {
            if ($value === null) continue;
            $admin->$key = $value;
        }

        if ($admin->save() === false) {
            throw new ApiException('修改失败', ResponseCode::SERVER_ERR);
        }

        return $this->responseSuccess(null, '修改成功');
    }

    /**
     * 获取菜单与权限节点
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function nav(Request $request)
    {
        $admin = $request->user('admin');
        $role = $admin->role;
        $role_id = $role->id;

        $menus = [];
        $perms = [];
        if ($role_id === 1) {
            $permissions = Authority::query()->get();
        } else {
            $permissions = $role->permissions->sortByDesc('order')->all();
        }

        foreach ($permissions as $permission) {
            if (!collect($menus)->contains('id', $permission->id)) {
                $menus[] = $permission;
            }
            //注入所有节点
            if (!in_array($permission->alias, $perms)) {
                $perms[] = $permission->alias;
            }
        }

        $menuList = $this->AuthorityFormat(array_values($menus));

        return $this->responseSuccess(['menus' => $menuList, 'perms' => $perms]);
    }

    /**
     * 权限格式化
     *
     * @param $items
     * @param int $pid
     * @return array
     */
    protected function AuthorityFormat($items, $pid = 0)
    {
        $list = [];
        foreach ($items as $item) {
            if ($item['pid'] == $pid) {
                $child = $this->AuthorityFormat($items, $item['id']);
                $item['list'] = $child ?: null;
                $list[] = $item;
            }
        }

        return $list;
    }
}
