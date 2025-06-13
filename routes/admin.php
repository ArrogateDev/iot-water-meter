<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OperationLogController;
use App\Http\Controllers\Admin\PersonalController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Principal Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/
//获取图形验证码
Route::get('get-captcha', [CommonController::class, 'captcha']);
//登录
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'abilities:admin']], function ($route) {

    //公共模块
    $route->group(['prefix' => 'main'], function ($route) {
        $route->get('get-role', [CommonController::class, 'getRole']);
    });

    //上传文件
    $route->post('upload', [UploadController::class, 'upload']);

    //账号信息
    $route->get('info', [PersonalController::class, 'info']);
    //管理员菜单
    $route->get('nav', [PersonalController::class, 'nav']);
    //修改账号信息
    $route->put('info', [PersonalController::class, 'update']);
    //退出登录
    $route->put('logout', [AuthController::class, 'logout']);

    //Dashboard
    $route->group(['prefix' => 'dashboard', 'middleware' => 'admin:DashboardManage'], function ($route) {
        $route->get('/', [DashboardController::class, 'index'])->middleware('admin:Dashboard');
        $route->get('/rank', [DashboardController::class, 'rank'])->middleware('admin:Dashboard');
    });

    //系统设置
    $route->group(['prefix' => 'system', 'middleware' => 'admin:SystemManage'], function ($route) {
        //角色管理
        $route->group(['prefix' => 'roles'], function ($route) {
            //角色列表
            $route->get('/', [RoleController::class, 'index'])->middleware('admin:RoleList');
            //创建角色
            $route->post('/', [RoleController::class, 'store'])->middleware('admin:RoleAdd');
            //修改角色
            $route->put('/{role}', [RoleController::class, 'update'])->middleware('admin:RoleEdit');
            //删除角色
            $route->delete('/{role}', [RoleController::class, 'destroy'])->middleware('admin:RoleDelete');
            //权限列表
            $route->get('/get-menus', [RoleController::class, 'menus'])->middleware('admin:RoleEdit');
        });
        //管理员模块
        $route->group(['prefix' => 'admin'], function ($route) {
            //管理员列表
            $route->get('/', [AdminController::class, 'index'])->middleware('admin:AdminList');
            //创建管理员
            $route->post('/', [AdminController::class, 'store'])->middleware('admin:AdminAdd');
            //修改管理员
            $route->put('/{admin}', [AdminController::class, 'update'])->middleware('admin:AdminEdit');
            //删除管理员
            $route->delete('/{admin}', [AdminController::class, 'destroy'])->middleware('admin:AdminDelete');
        });
        //操作日志
        $route->get('/operation-log', [OperationLogController::class, 'index'])->middleware('admin:OperationLog');
    });
});
