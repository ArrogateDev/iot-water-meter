<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseCode;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Manage\Admin;
use App\Models\Manage\Authority;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * 登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiException
     */
    public function login(Request $request)
    {
        $account = $request->input('username');
        $password = $request->input('password');

        $code_key = (string)$request->input('imgCodeKey');
        $rules = ['imgCode' => 'required|captcha_api:' . $code_key . ',math'];
        $validator = validator()->make($request->all(), $rules);

        if (app()->environment('production') && $validator->fails()) {
            throw new ApiException('验证码错误', ResponseCode::VERIFICATION_CODE_ERROR);
        }

        if (!$account || !$password) {
            throw new ApiException('账号或者密码错误', ResponseCode::ACCOUNT_OR_PASSWORD_ERROR);
        }

        $admin = Admin::query()->where('account', $account)->firstOr(function () {
            throw new ApiException('账号或者密码错误', ResponseCode::ACCOUNT_OR_PASSWORD_ERROR);
        });

        try {

            if ($admin->status !== Admin::NORMAL) {
                throw new ApiException('账号或者密码错误', ResponseCode::FORBIDDEN);
            }

            if (!Hash::check($password, $admin->password)) {
                throw new ApiException('账号或者密码错误', ResponseCode::PARAM_ERR);
            }

            $admin->makeHidden(['status', 'is_delete', 'created_at', 'updated_at']);

            $admin->tokens()->delete();
            $token = $admin->createToken(env('APP_NAME'), ['admin']);

            $role = $admin->role;
            $authority = $admin->role_authority;

            $result = [
                'username' => $account,
                'token' => 'Bearer ' . $token->plainTextToken,
                'role' => $role->name,
                'roleId' => $role->id,
                'permissions' => $role->id === 1 ? Authority::query()->get()->pluck('alias')->toArray() : $authority
            ];

            return $this->responseSuccess($result);
        } catch (ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error($e);
            throw new ApiException('登录失败', ResponseCode::LOGIN_FAIL);
        }
    }

    /**
     * 退出登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $admin = $request->user('admin');
        $admin->tokens()->delete();

        return $this->responseSuccess(null, '退出成功');
    }

    /**
     * Horizon面板认证
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function horizonAuth(Request $request)
    {
        $admin = $request->user('admin');
        if (!$admin->auth_permission) {
            $admin->remove_tokens('admin');
        }

        $login_data['type'] = $request->input('type');
        $login_data['a'] = $admin->id;
        $login_data['t'] = Carbon::now()->toDateTimeString();
        $url = url('/horizon-quick-login?s=' . Crypt::encryptString(json_encode($login_data)));

        return $this->responseSuccess($url, 'ok');
    }
}
