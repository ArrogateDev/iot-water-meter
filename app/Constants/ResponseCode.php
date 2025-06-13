<?php

namespace App\Constants;

/**
 * Api 响应业务状态码
 */
class ResponseCode
{
    //通用
    /**
     * 请求成功
     */
    public const SUCCESS = 0;

    /**
     * 用户未授权
     */
    const UNAUTH = 401;

    /**
     * 非法操作
     */
    const ILLEGAL_OPERATION = 4002;

    /**
     * 拒绝访问
     */
    const FORBIDDEN = 4403;

    /**
     * 未找到相关资源
     */
    const NOT_FOUND = 4404;

    /**
     * 请求方法允许通过
     */
    const METHOD_NOT_ALLOWED = 4005;

    /**
     * 客户端请求参数错误
     */
    const PARAM_ERR = 4422;

    /**
     * 操作频繁
     */
    const FREQUENTLY = 4408;

    /**
     * 操作失败
     */
    const FAIL = 4406;

    /**
     * 签名错误
     */
    const SIGN_ERROR = 4601;

    /**
     * 签名已过期
     */
    const SIGN_EXPIRED = 4602;
    //通用

    //用户相关
    /**
     * 账号不存在
     */
    const USER_DOES_NOT_EXIST = 4503;

    /**
     * 账号已禁用
     */
    const ACCOUNT_IS_DISABLED = 4504;

    /**
     * 账号或者密码错误
     */
    const ACCOUNT_OR_PASSWORD_ERROR = 4505;

    /**
     * 登录失败
     */
    const LOGIN_FAIL = 4506;

    /**
     * 验证码错误
     */
    const VERIFICATION_CODE_ERROR = 4507;
    //用户相关

    /**
     * 服务器内部错误
     */
    const SERVER_ERR = 5000;
}
