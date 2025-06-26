<?php

namespace App\Constants;
/**
 * WebSocket 响应业务类型
 */
class WebSocketResponseType
{
    /**
     * 心跳成功
     */
    const PING_OK = 'heartbeat-ack';

    /**
     * 认证成功
     */
    const AUTH_SUCCESS = 'auth_success';

    /**
     * 认证失败
     */
    const AUTH_FAIL = 'auth_fail';

    /**
     * token失效
     */
    const TOKEN_EXPIRED = 'token_expired';

    /**
     * 服务器异常
     */
    const SERVER_ERR = 'server_err';

    /**
     * 非法操作
     */
    const ILLEGAL_OPERATION = 'illegal_operation';

    /**
     * 服务器拥堵
     */
    const SERVER_CONGESTION = 'server_congestion';
}
