<?php

namespace App\Constants;
/**
 * WebSocket 响应业务状态码
 */
class WebSocketResponseCode
{

    /**
     * 请求成功
     */
    const SUCCESS = 0;

    /**
     * 需要认证
     */
    const AUTH = 70000;

    /**
     * 认证失败
     */
    const AUTH_FAIL = 70001;

    /**
     * token已过期
     */
    const TOKEN_EXPIRED = 70002;

    /**
     * 非法操作
     */
    const ILLEGAL_OPERATION = 70004;

    /**
     * 操作失败
     */
    const FAIL = 70005;
}
