<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseCode;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Manage\Authority;
use App\Models\Manage\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * 列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $name = $request->query('name');
        $created_at = $request->query('created_at');

        $list = Role::query()
            ->with('permissions')
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($created_at, function ($query) use ($created_at) {
                $query->whereDate('created_at', $created_at);
            })
            ->paginate(limit_page());

        return $this->responseSuccess($list);
    }

    /**
     * 创建
     *
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleRequest $request)
    {
        $admin_id = $request->user('admin')->id;
        if (!(($lock = Cache::lock("submit_role_store_lock:$admin_id", 360))->get())) {
            throw new ApiException(__('Frequent operation, please try again later'), ResponseCode::FREQUENTLY);
        }

        // 请求结束后关闭锁
        def($_Context, function () use (&$lock) {
            $lock->release();
        });

        $inputs = $request->only(['name', 'status']);

        try {

            $role = new Role();
            foreach ($inputs as $field => $value) {
                $role->$field = $value;
            }

            if ($role->save() === false) {
                throw new \Exception('role:failed');
            }

            $permissions = $request->input('menu');
            if (!empty($permissions)) {
                $permissions = array_unique($permissions);
                $permissions = Authority::query()->whereIn('id', $permissions)->pluck('id')->toArray();

                $role->permissions()->sync($permissions);
            }

            return $this->responseSuccess(null, '成功');
        } catch (\Exception $e) {
            Log::error($e);
            throw new ApiException('创建失败', ResponseCode::SERVER_ERR);
        }
    }

    /**
     * 修改
     *
     * @param RoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleRequest $request, Role $role)
    {
        if (!(($lock = Cache::lock("submit_role_update_lock:$role->id", 360))->get())) {
            throw new ApiException(__('Frequent operation, please try again later'), ResponseCode::FREQUENTLY);
        }

        // 请求结束后关闭锁
        def($_Context, function () use (&$lock) {
            $lock->release();
        });

        $inputs = $request->only(['name', 'status']);

        try {

            foreach ($inputs as $field => $value) {
                $role->$field = $value;
            }

            if ($role->save() === false) {
                throw new \Exception('role:failed');
            }

            $permissions = $request->input('permissions');
            if (!empty($permissions)) {
                $permissions = array_unique($permissions);
                $permissions = Authority::query()->whereIn('id', $permissions)->pluck('id')->toArray();

                $role->permissions()->sync($permissions);
            }

            return $this->responseSuccess(null, '成功');
        } catch (\Exception $e) {
            Log::error($e);
            throw new ApiException('修改失败', ResponseCode::SERVER_ERR);
        }
    }

    /**
     * 删除
     *
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Role $role)
    {
        if (!(($lock = Cache::lock("submit_role_destroy_lock:$role->id", 360))->get())) {
            throw new ApiException(__('Frequent operation, please try again later'), ResponseCode::FREQUENTLY);
        }

        // 请求结束后关闭锁
        def($_Context, function () use (&$lock) {
            $lock->release();
        });

        if ($role->id === 1) {
            throw new ApiException('默认角色，不能删除', ResponseCode::SERVER_ERR);
        }

        try {

            if ($role->admins) {
                foreach ($role->admins as $admin) {
                    $admin->roles()->detach($role->id);
                }
            }

            $role->permissions()->detach($role->id);
            $role->delete();

            return $this->responseSuccess(null, '删除成功');
        } catch (\Exception $e) {
            throw new ApiException('删除失败', $e->getCode());
        }
    }

    /**
     * 菜单
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function menus(Request $request)
    {

        $name = $request->query('name');
        $type = $request->query('type', 1);
        $admin = $request->user('admin');
        $role = $admin->role;
        $role_id = $role->id;

        $authorities = Authority::query()
            ->with(['parent'])
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($type === 1 && $role_id !== 1, function ($query) use ($role_id) {
                return $query->whereHasIn('authority_roles', function ($query) use ($role_id) {
                    return $query->where('role_id', $role_id);
                });
            })
            ->orderByDesc('sort')
            ->orderBy('id')
            ->get();

        $authorities = $authorities->filter(function ($authority) {
            if ($authority->pid > 0) {
                return $authority->parent != null;
            }
            return true;
        });

        $authorities->makeHidden(['parent']);

        return $this->responseSuccess(array_values(json_decode($authorities, true)));
    }
}
