<?php

namespace App\Http\Middleware;

use App\Constants\ResponseCode;
use App\Events\Manage\AdminLogEvent;
use App\Exceptions\ApiException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionAuth
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param $alias
     * @return mixed
     * @throws ApiException
     */
    public function handle(Request $request, Closure $next, $alias)
    {
        $admin = $request->user('admin');
        $role = $admin->role;
        $role_id = $role->id;

        //验证权限,超级管理员不验证权限
        $is_record = !Str::endsWith($alias, 'Manage');
        if ($role_id != 1 && !$admin->hasPermissions($alias)) {
            $is_record && event(new AdminLogEvent($request, $alias, 0));
            throw new ApiException('无权操作', ResponseCode::FORBIDDEN);
        }

        $is_record && event(new AdminLogEvent($request, $alias));
        return $next($request);
    }
}
