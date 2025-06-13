<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Models\Manage\Role;
use Illuminate\Http\Request;
use Mews\Captcha\Facades\Captcha;

class CommonController extends Controller
{
    /**
     * 图形验证码
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiException
     */
    public function captcha(Request $request)
    {
        return $this->responseSuccess(Captcha::create($request->input('type', 'admin'), true));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRole()
    {
        $list = Role::query()
            ->orderBy('id')
            ->select('id as value', 'name as label')
            ->get();

        return $this->responseSuccess($list);
    }
}
